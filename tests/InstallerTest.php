<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/class/SqliteDb.php';
require_once __DIR__ . '/../includes/class/SqliteSchemaConverter.php';
require_once __DIR__ . '/../includes/class/Installer.php';

final class InstallerTest extends TestCase
{
    private $dbPath;
    private $schema;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/sr_installer_test_' . getmypid() . '.sqlite';
        @unlink($this->dbPath);
        $this->schema = file_get_contents(__DIR__ . '/../includes/install/DB.sql');
    }

    protected function tearDown(): void
    {
        @unlink($this->dbPath);
    }

    public function testSchemaTablesListsEveryGameTable(): void
    {
        $tables = Installer::schemaTables($this->schema);

        $this->assertCount(99, $tables);
        $this->assertContains('users', $tables);
        $this->assertContains('user_credentials', $tables);
        $this->assertNotContains('hacker_quotes', $tables, 'the random quote system was removed');
    }

    public function testFreshDatabaseIsNotInstalled(): void
    {
        $db = new SqliteDb($this->dbPath);

        $this->assertFalse(Installer::isInstalled($db));
        $this->assertFalse(Installer::hasPlayers($db));
    }

    public function testImportCreateAdminAndRetry(): void
    {
        $db = new SqliteDb($this->dbPath);
        Installer::importSqlite($db, $this->schema);

        $this->assertTrue(Installer::isInstalled($db));
        $this->assertFalse(Installer::hasPlayers($db));

        // A retry after a half-finished attempt must not fail on existing tables
        Installer::importSqlite($db, $this->schema);

        $uid = Installer::createAdmin($db, 'sradmin', 'secret12', 'admin@example.test');
        $this->assertNotEmpty($uid);
        $this->assertTrue(Installer::hasPlayers($db));

        $credentials = $db->where('uid', $uid)->getOne('user_credentials');
        $this->assertSame(1, (int) $credentials['group_id']);
        $this->assertTrue(password_verify('secret12', $credentials['password']));
    }

    public function testBeginnerOrganizationsHaveADefaultRank(): void
    {
        $db = new SqliteDb($this->dbPath);
        Installer::importSqlite($db, $this->schema);

        foreach ($db->get('org_zone_beginners') as $beginner) {
            $rank = $db->where('org_id', $beginner['org_id'])->where('default_rank', 1)->getOne('org_ranks');
            $this->assertNotEmpty($rank, 'beginner org ' . $beginner['org_id'] . ' needs a default rank for new players');
        }
    }

    public function testInstalledMarkerIsWrittenOnlyWhenInstalled(): void
    {
        $cacheDir = sys_get_temp_dir() . '/sr_installer_cache_' . getmypid();
        @mkdir($cacheDir);
        @unlink($cacheDir . '/.installed');
        $db = new SqliteDb($this->dbPath);

        $this->assertFalse(Installer::isInstalledCached($db, $cacheDir));
        $this->assertFileDoesNotExist($cacheDir . '/.installed');

        Installer::importSqlite($db, $this->schema);
        $this->assertTrue(Installer::isInstalledCached($db, $cacheDir));
        $this->assertFileExists($cacheDir . '/.installed');

        @unlink($cacheDir . '/.installed');
        @rmdir($cacheDir);
    }

    public function testValidateAdmin(): void
    {
        $this->assertSame(array(), Installer::validateAdmin('sradmin', 'secret12', 'admin@example.test'));
        $this->assertCount(3, Installer::validateAdmin('ab', 'x', 'nope'));
        $this->assertNotEmpty(Installer::validateAdmin('bad name', 'secret12', 'admin@example.test'));
        $this->assertNotEmpty(Installer::validateAdmin('sradmin', str_repeat('x', 21), 'admin@example.test'));
    }

    public function testConfigFileCannotBeUsedForCodeInjection(): void
    {
        $config = array(
            'driver'      => 'mysql',
            'server_name' => 'db',
            'username'    => 'root',
            'password'    => "x'; system('id'); //",
            'name'        => 'sr',
            'port'        => 3306,
            'sqlite_path' => '',
        );
        $file = sys_get_temp_dir() . '/sr_db_info_' . getmypid() . '.php';
        file_put_contents($file, Installer::buildConfigFile($config));

        $loaded = (static function ($path) { return require $path; })($file);
        unlink($file);

        $this->assertSame($config, $loaded);
    }
}

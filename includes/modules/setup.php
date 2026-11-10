<?php

/**
 * First-run setup wizard (/setup).
 *
 * - No includes/database_info.php: asks for the database (MySQL or SQLite)
 *   and the admin account, imports includes/install/DB.sql, writes the config.
 * - database_info.php present (e.g. generated from DB_* environment variables
 *   by the Docker entrypoint) but no game tables yet: only asks for the admin
 *   account and imports the schema into the configured database.
 * - Already installed: redirects home. A database that already has players is
 *   never dropped or overwritten.
 */

require_once ABSPATH . 'includes/class/Installer.php';
require_once ABSPATH . 'includes/class/SqliteDb.php';
require_once ABSPATH . 'includes/class/SqliteSchemaConverter.php';

$configPath = ABSPATH . 'includes/database_info.php';
$dbFile = ABSPATH . 'includes/install/DB.sql';
$configured = file_exists($configPath);

$container->tVars['display'] = 'setup.tpl';
$container->tVars['setupConfigured'] = $configured;

if (!file_exists($dbFile)) {
    $container->errors[] = 'includes/install/DB.sql is missing.';
    return;
}

if ($configured && Installer::isInstalled($container->db())) {
    $cardinal->redirect(URL);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$adminUser = trim((string) ($_POST['ADMIN_USER'] ?? ''));
$adminPass = (string) ($_POST['ADMIN_PASS'] ?? '');
$adminEmail = trim((string) ($_POST['ADMIN_EMAIL'] ?? ''));

foreach (Installer::validateAdmin($adminUser, $adminPass, $adminEmail) as $message) {
    $container->errors[] = $message;
}
if ($container->errors) {
    return;
}

$schema = file_get_contents($dbFile);

try {
    if ($configured) {
        $db = $container->db();
        if (Installer::isSqlite($db)) {
            Installer::importSqlite($db, $schema);
        } else {
            Installer::importMysql($db->mysqli(), $schema);
        }
    } else {
        $driver = strtolower(trim((string) ($_POST['DB_DRIVER'] ?? 'mysql'))) === 'sqlite' ? 'sqlite' : 'mysql';
        $dbConfig = array(
            'driver'      => $driver,
            'server_name' => '',
            'username'    => '',
            'password'    => '',
            'name'        => '',
            'port'        => 3306,
            'sqlite_path' => '',
        );

        if ($driver === 'sqlite') {
            $sqlitePath = trim((string) ($_POST['SQLITE_PATH'] ?? '')) ?: 'includes/local.sqlite';
            $dbConfig['sqlite_path'] = $sqlitePath;
            $db = new SqliteDb($sqlitePath);
            if (Installer::hasPlayers($db)) {
                throw new InvalidArgumentException('That SQLite file already contains a Secret Republic game with players. Choose another path.');
            }
            Installer::importSqlite($db, $schema);
        } else {
            $dbConfig['server_name'] = trim((string) ($_POST['DB_HOST'] ?? ''));
            $dbConfig['username'] = trim((string) ($_POST['DB_USER'] ?? ''));
            $dbConfig['password'] = (string) ($_POST['DB_PASS'] ?? '');
            $dbConfig['name'] = trim((string) ($_POST['DB_NAME'] ?? ''));
            $dbConfig['port'] = (int) ($_POST['DB_PORT'] ?? 0) ?: 3306;

            if ($dbConfig['server_name'] === '' || $dbConfig['username'] === '' || $dbConfig['name'] === '') {
                throw new InvalidArgumentException('Database host, user and name are required for MySQL.');
            }

            $db = new Mysqlidb($dbConfig['server_name'], $dbConfig['username'], $dbConfig['password'], $dbConfig['name'], $dbConfig['port']);
            try {
                $mysqli = $db->mysqli();
            } catch (Throwable $connectError) {
                throw new InvalidArgumentException('Could not connect to MySQL: ' . $connectError->getMessage());
            }

            if (Installer::hasPlayers($db)) {
                throw new InvalidArgumentException('That database already contains a Secret Republic game with players. Use an empty database.');
            }
            Installer::importMysql($mysqli, $schema);
        }

        if (@file_put_contents($configPath, Installer::buildConfigFile($dbConfig)) === false) {
            throw new RuntimeException('Could not write includes/database_info.php. Make sure the web server can write to the includes/ folder.');
        }

        $cardinal = new Cardinal($container);
        $container->set('cardinal', $cardinal);
        $db = $container->db();
    }

    Installer::createAdmin($db, $adminUser, $adminPass, $adminEmail);
} catch (InvalidArgumentException $ex) {
    $container->errors[] = $ex->getMessage();
    return;
} catch (Throwable $ex) {
    error_log('Secret Republic setup failed: ' . $ex->getMessage());
    $container->errors[] = 'Setup failed: ' . $ex->getMessage();
    return;
}

$cardinal->redirect(URL);

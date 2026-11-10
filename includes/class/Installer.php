<?php

/**
 * First-run installation helpers shared by public_html/index.php and the
 * /setup module.
 *
 * The game counts as installed once includes/database_info.php exists AND the
 * configured database contains the `users` table. A database that already has
 * players is never dropped or overwritten.
 */
class Installer
{
	public static function isSqlite($db)
	{
		return $db instanceof SqliteDb;
	}

	public static function tableExists($db, $table)
	{
		if (self::isSqlite($db)) {
			$rows = $db->rawQuery("SELECT name FROM sqlite_master WHERE type='table' AND name = ?", array($table));
		} else {
			$rows = $db->rawQuery('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', array($table));
		}

		return !empty($rows);
	}

	public static function isInstalled($db)
	{
		return self::tableExists($db, 'users');
	}

	/**
	 * isInstalled() with a marker file in includes/cache so a working install
	 * doesn't pay for the check on every request.
	 */
	public static function isInstalledCached($db, $cacheDir)
	{
		$marker = rtrim($cacheDir, '/') . '/.installed';
		if (is_file($marker)) {
			return true;
		}
		if (!self::isInstalled($db)) {
			return false;
		}
		@file_put_contents($marker, gmdate('c'));

		return true;
	}

	/** True when the database already holds a game with at least one player. */
	public static function hasPlayers($db)
	{
		if (!self::isInstalled($db)) {
			return false;
		}
		$row = $db->rawQuery('SELECT COUNT(*) AS c FROM users');

		return isset($row[0]['c']) && (int) $row[0]['c'] > 0;
	}

	/**
	 * Table names created by the MySQL schema dump.
	 *
	 * @return string[]
	 */
	public static function schemaTables($sql)
	{
		preg_match_all('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?([A-Za-z0-9_]+)`?/i', $sql, $m);

		return array_values(array_unique($m[1]));
	}

	/**
	 * Validate the admin account fields with the same rules as registration.
	 *
	 * @return string[] error messages (empty when valid)
	 */
	public static function validateAdmin($username, $password, $email)
	{
		$errors = array();
		$username = (string) $username;
		$password = (string) $password;

		if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15) {
			$errors[] = 'Admin username must contain only letters/numbers and have between 4 and 15 characters.';
		}
		if (strlen($password) < 4 || strlen($password) > 20) {
			$errors[] = 'Admin password must be between 4 and 20 characters.';
		}
		if (!filter_var((string) $email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Admin email address is not valid.';
		}

		return $errors;
	}

	/**
	 * Import the MySQL schema. Only tables that belong to the game schema are
	 * dropped first (to recover from a half-finished earlier attempt); other
	 * tables in the database are left alone.
	 */
	public static function importMysql(mysqli $mysqli, $sql)
	{
		$mysqli->query('SET FOREIGN_KEY_CHECKS = 0');
		foreach (self::schemaTables($sql) as $table) {
			$mysqli->query('DROP TABLE IF EXISTS `' . $mysqli->real_escape_string($table) . '`');
		}
		$mysqli->query('SET FOREIGN_KEY_CHECKS = 1');

		if (!$mysqli->multi_query($sql)) {
			throw new Exception('SQL import failed: ' . $mysqli->error);
		}
		do {
			if ($result = $mysqli->store_result()) {
				$result->free();
			}
			if ($mysqli->errno) {
				throw new Exception('SQL import error: ' . $mysqli->error);
			}
		} while ($mysqli->more_results() && $mysqli->next_result());

		if ($mysqli->errno) {
			throw new Exception('SQL import error: ' . $mysqli->error);
		}
	}

	public static function importSqlite(SqliteDb $db, $sql)
	{
		foreach (self::schemaTables($sql) as $table) {
			$db->rawQuery('DROP TABLE IF EXISTS "' . $table . '"');
		}
		foreach (SqliteSchemaConverter::convertMySqlDump($sql) as $statement) {
			$db->rawQuery($statement);
		}
	}

	/**
	 * Create the first administrator (group 1). RegistrationSystem::addUser()
	 * needs game state (grid nodes, zones) that a fresh install doesn't have,
	 * so the essentials are inserted directly.
	 *
	 * @return int|false new user id
	 */
	public static function createAdmin($db, $username, $password, $email)
	{
		$uid = $db->insert('users', array(
			'username'    => $username,
			'zone'        => 1,
			'money'       => 1000,
			'energy'      => 100,
			'maxEnergy'   => 100,
			'expNext'     => 62,
			'level'       => 1,
			'skillPoints' => 5,
			'alphaCoins'  => 10000,
			'gavatar'     => md5(strtolower($email)),
			'createdAt'   => time(),
		));

		if ($uid) {
			$db->insert('user_credentials', array(
				'uid'             => $uid,
				'password'        => password_hash($password, PASSWORD_DEFAULT),
				'group_id'        => 1,
				'email'           => $email,
				'email_confirmed' => 1,
				'pin'             => md5((string) random_int(1000, 9999)),
			));
		}

		return $uid;
	}

	/** Render includes/database_info.php with var_export so values can't inject code. */
	public static function buildConfigFile(array $dbConfig)
	{
		$lines = array('<?php', '// Generated by the Secret Republic setup wizard on ' . gmdate('Y-m-d H:i:s') . ' UTC.', '');
		foreach ($dbConfig as $key => $value) {
			$lines[] = '$db[' . var_export((string) $key, true) . '] = ' . var_export($value, true) . ';';
		}
		$lines[] = '';
		$lines[] = 'return $db;';
		$lines[] = '';

		return implode(PHP_EOL, $lines);
	}
}

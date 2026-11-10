<?php
/**
 * Database configuration for Docker deployments.
 *
 * The container entrypoint copies this file to includes/database_info.php when
 * DB_HOST (MySQL) or DB_DRIVER=sqlite is set, so the connection comes from
 * environment variables and /setup only asks for the admin account.
 */
$env = static function ($key, $default = '') {
	$value = getenv($key);
	return $value === false || $value === '' ? $default : $value;
};

$db['driver'] = strtolower($env('DB_DRIVER', 'mysql')) === 'sqlite' ? 'sqlite' : 'mysql';
$db['server_name'] = $env('DB_HOST', 'db');
$db['username'] = $env('DB_USER', 'secretrepublic');
$db['password'] = $env('DB_PASS');
$db['name'] = $env('DB_NAME', 'secretrepublic');
$db['port'] = (int) $env('DB_PORT', '3306');
$db['sqlite_path'] = $env('SQLITE_PATH', 'includes/local.sqlite');

return $db;

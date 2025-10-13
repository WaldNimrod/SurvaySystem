<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. e.g.: mysqli.
|			Currently supported:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Query Builder class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['encrypt']  Whether or not to use an encrypted connection.
|
|			'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
|			'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
|
|				'ssl_key'    - Path to the private key file
|				'ssl_cert'   - Path to the public key certificate file
|				'ssl_ca'     - Path to the certificate authority file
|				'ssl_capath' - Path to a directory containing trusted CA certificats in PEM format
|				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
|				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not ('mysqli' only)
|
|	['compress'] Whether or not to use client compression (MySQL only)
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['ssl_options']	Used to set various SSL options that can be used when making SSL connections.
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|	['save_queries'] TRUE/FALSE - Whether to "save" all executed queries.
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
| 				When you run a query, with this setting set to TRUE (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/
$active_group = 'default';


$query_builder = true;
$host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
// strip port if exists (e.g., localhost:8000)
if (strpos($host, ':') !== false) {
    $host_no_port = explode(':', $host)[0];
} else {
    $host_no_port = $host;
}

if ($host_no_port === 'mhkl.mezoo.co.il') {
    $frag = 'mhklmezo';
}

if ($host_no_port === 'survay.mezoo.co.il') {
    $frag = 'survayme';
}

if ($host_no_port === 'till.mezoo.co.il' || $host_no_port === 'www.till.mezoo.co.il') {
    $frag = 'tillmezo';
}

// Defaults
$default_host = ($host_no_port === 'worldclass' || $host_no_port === 'localhost') ? '127.0.0.1' : 'localhost';
$default_user = ($host_no_port === 'localhost' || $host_no_port === 'worldclass') ? 'root' : $frag . '_a';
$default_pass = ($host_no_port === 'localhost' || $host_no_port === 'worldclass') ? 'root' : 'E22&77zRu@sC';
$default_name = ($host_no_port === 'localhost' || $host_no_port === 'worldclass') ? 'mezoo' : $frag . '_a';
$default_port = ($host_no_port === 'localhost' || $host_no_port === 'worldclass') ? 3306 : 3306;

// Environment overrides for local/dev if needed
$env_host = getenv('MEZOO_DB_HOST');
$env_user = getenv('MEZOO_DB_USER');
$env_pass = getenv('MEZOO_DB_PASS');
$env_name = getenv('MEZOO_DB_NAME');
$env_port = getenv('MEZOO_DB_PORT');

$db['default'] = array(
    'dsn' => '',
    'hostname' => $env_host ? $env_host : $default_host,
    'username' => $env_user ? $env_user : $default_user,
    'password' => $env_pass ? $env_pass : $default_pass,
    'database' => $env_name ? $env_name : $default_name,
    'port' => $env_port ? (int)$env_port : $default_port,
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => false,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
);

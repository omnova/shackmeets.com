<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'pdo',
    'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'        => 'mysql:host=' . Shackmeetsconfig::dbHostname . ';dbname=' . Shackmeetsconfig::dbDatabase,
			'username'   => Shackmeetsconfig::dbUsername,
			'password'   => Shackmeetsconfig::dbPassword,
			'persistent' => FALSE,
		),
		// 'connection' => array(
			// /**
			 // * The following options are available for MySQL:
			 // *
			 // * string   hostname     server hostname, or socket
			 // * string   database     database name
			 // * string   username     database username
			 // * string   password     database password
			 // * boolean  persistent   use persistent connections?
			 // * array    variables    system variables as "key => value" pairs
			 // *
			 // * Ports and sockets may be appended to the hostname.
			 // */
			// 'hostname'   => Shackmeetsconfig::dbHostname,
			// 'database'   => Shackmeetsconfig::dbDatabase,
			// 'username'   => Shackmeetsconfig::dbUsername,
			// 'password'   => Shackmeetsconfig::dbPassword,
			// 'persistent' => FALSE,
		// ),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'alternate' => array(
		'type'       => 'pdo',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'        => 'mysql:host=' . Shackmeetsconfig::dbHostname . ';dbname=' . Shackmeetsconfig::dbDatabase,
			'username'   => Shackmeetsconfig::dbUsername,
			'password'   => Shackmeetsconfig::dbPassword,
			'persistent' => FALSE,
		),
		/**
		 * The following extra options are available for PDO:
		 *
		 * string   identifier  set the escaping identifier
		 */
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);
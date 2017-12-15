<?php
date_default_timezone_set('UTC');

//----------------------------
// DATABASE CONFIGURATION
//----------------------------

/*

Valid types (adapters) are Postgres & MySQL:

'type' must be one of: 'pgsql' or 'mysql' or 'sqlite'

*/

$ssp_config = new \w34u\ssp\Configuration();
return array(
    'db' => array(
        'development' => array(
            'type' => 'mysql',
            'host' => $ssp_config->dsnHostName,
            'port' => 3306,
            'database' => $ssp_config->dsnDatabaseName,
            'user' => $ssp_config->dsnUser,
            'password' => $ssp_config->dsnPassword,
            'charset' => $ssp_config->connectionEncoding,
            'directory' => 'ssp',
            //'socket' => '/var/run/mysqld/mysqld.sock'
        ),
        'pg_test' => array(
            'type' => 'pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'database' => 'ruckusing_migrations_test',
            'user' => 'postgres',
            'password' => '',
            //'directory' => 'custom_name',

        ),
        'mysql_test' => array(
            'type' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'ruckusing_migrations_test',
            'user' => 'root',
            'password' => '',
            //'directory' => 'custom_name',
            //'socket' => '/var/run/mysqld/mysqld.sock'
        ),
        'sqlite_test' => array(
            'type' => 'sqlite',
            'database' => RUCKUSING_WORKING_BASE . '/test.sqlite3',
            'host' => 'localhost',
            'port' => '',
            'user' => '',
            'password' => ''
        )

    ),
    'migrations_dir' => array('default' => RUCKUSING_WORKING_BASE . '/migrations'),
    'db_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db',
    'log_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'logs',
    'ruckusing_base' => $path_to_vendor_directory. 'ruckusing/ruckusing-migrations'
);

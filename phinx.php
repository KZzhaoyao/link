<?php

$config = include __DIR__ . '/app/config.php';

$db = $config['database'];

return array(
    'paths' => array(
        'migrations' => 'app/migrations'
    ),
    'environments' => array(
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'dev' => array(
            'adapter' => 'mysql',
            'host' => $db['host'],
            'name' => $db['dbname'],
            'user' => $db['user'],
            'pass' => $db['password'],
            'port' => isset($db['port']) ? $db['port'] : 3306,
            'charset' => $db['charset']
        )
    )
);
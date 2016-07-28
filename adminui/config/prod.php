<?php

use Silex\Provider\MonologServiceProvider;

// configure your app for the production environment
$app['var.path'] = dirname(dirname(__DIR__)) . '/var';

$app['twig.path'] = array(realpath(__DIR__.'/../templates'));
$app['twig.options'] = array('cache' => $app['var.path'] . '/cache/adminui-twig');

$app['asset_version'] = 4;

if (file_exists(__DIR__ . '/paramaters.php')) {
    include __DIR__ . '/paramaters.php';
}

if (file_exists(__DIR__ . '/ipWhiteList.php')) {
    include __DIR__ . '/ipWhiteList.php';
}

$app->register(new MonologServiceProvider());

$app['monolog.name'] = 'AdminUI';
$app['monolog.logfile'] = $app['var.path'] . '/logs/rootapi.log';
$app['monolog.level'] = 'NOTICE';

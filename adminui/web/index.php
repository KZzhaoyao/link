<?php

ini_set('display_errors', 0);

use EdusohoNet\Service\Common\ServiceKernel;

require_once __DIR__.'/../../vendor/autoload.php';

$serviceConfig = include __DIR__ . '/../../app/config.php';
$serviceKernel = ServiceKernel::create('prod', true);
$serviceKernel->setParameterBag($serviceConfig);

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
$app->run();

#!/usr/bin/env php
<?php

use Asynchronous\Server\AsyncTaskServer;
use EdusohoNet\Service\Common\ServiceKernel;

require_once __DIR__.'/../vendor/autoload.php';


$serviceConfig = include __DIR__ . '/../app/config.php';
$serviceKernel = ServiceKernel::create('prod', true);
$serviceKernel->setParameterBag($serviceConfig);


$server = new AsyncTaskServer($serviceConfig['async']['serverConfig']);

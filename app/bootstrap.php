<?php

use Symfony\Component\Debug\Debug;
use EdusohoNet\Service\Common\ServiceKernel;

require_once __DIR__.'/../vendor/autoload.php';

Debug::enable();

$serviceConfig = include __DIR__ . '/config.php';
$serviceKernel = ServiceKernel::create('dev', true);
$serviceKernel->setParameterBag($serviceConfig);


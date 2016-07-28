<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use EdusohoNet\Service\Common\ServiceKernel;
use AdminUI\Util\DataDict;

date_default_timezone_set('Asia/Shanghai');

$app = new Application();
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addFunction(new Twig_SimpleFunction('dict', function ($type) {
        return DataDict::dict($type);
    }));

    $twig->addFunction(new Twig_SimpleFunction('dict_text', function ($type, $key) {
        return DataDict::text($type, $key);
    }));

    $twig->addFunction(new Twig_SimpleFunction('select_options', function ($choices, $selected = null, $empty = null) {
        $html = '';
        if (!is_null($empty)) {
            $html .= "<option value=\"\">{$empty}</option>";
        }
        foreach ($choices as $value => $name) {
            if ($selected == $value) {
                $html .= "<option value=\"{$value}\" selected=\"selected\">{$name}</option>";
            } else {
                $html .= "<option value=\"{$value}\">{$name}</option>";
            }
        }

        return $html;
    }, array('is_safe' => array('html'))));

    $twig->addFunction(new Twig_SimpleFunction('radios', function ($name, $choices, $checked) {
        $html = '';
        foreach ($choices as $value => $label) {
            if ($checked == $value) {
                $html .= "<label class='radio-inline'><input type=\"radio\" name=\"{$name}\" value=\"{$value}\" checked=\"checked\"> {$label}</label>";
            } else {
                $html .= "<label class='radio-inline'><input type=\"radio\" name=\"{$name}\" value=\"{$value}\"> {$label}</label>";
            }
        }
        return $html;
    }, array('is_safe' => array('html'))));

    $twig->addFunction(new Twig_SimpleFunction('checkboxs', function ($name, $choices, $checkeds = array()) {
        $html = '';
        if (!is_array($checkeds)) {
            $checkeds = array($checkeds);
        }

        foreach ($choices as $value => $label) {
            if (in_array($value, $checkeds)) {
                $html .= "<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"{$value}\" checked=\"checked\"> {$label}</label>";
            } else {
                $html .= "<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"{$value}\"> {$label}</label>";
            }
        }
        return $html;
    }, array('is_safe' => array('html'))));

    $twig->addFunction(new Twig_SimpleFunction('admin_name', function ($id) use ($app) {
        if (empty($id)) {
            return '';
        }
        $admin = ServiceKernel::instance()->createService('Admin.AdminService')->getAdmin($id);
        if (!empty($admin)) {
            return $admin['username'];
        }

        return '--';
    }));

    $twig->addFilter(new Twig_SimpleFilter('file_size', function ($size) use ($app) {
        $currentValue = $currentUnit = null;
        $unitExps = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3);
        foreach ($unitExps as $unit => $exp) {
            $divisor = pow(1000, $exp);
            $currentUnit = $unit;
            $currentValue = $size / $divisor;
            if ($currentValue < 1000) {
                break;
            }
        }

        return sprintf('%.1f', $currentValue) . $currentUnit;
    }));

    $twig->addFilter(new Twig_SimpleFilter('duration', function ($value) use ($app) {
        $minutes = intval($value / 60);
        $seconds = $value - $minutes * 60;
        if ($minutes === 0) {
            return $seconds . '秒';
        }
        return "{$minutes}分钟{$seconds}秒";
    }));



    $twig->addFunction(new Twig_SimpleFunction('user_setting', function ($userId, $key, $default) use ($app) {
        return ServiceKernel::instance()->createService('User.UserSettingService')->get($userId, $key, $default);
    }));



    return $twig;
}));

return $app;
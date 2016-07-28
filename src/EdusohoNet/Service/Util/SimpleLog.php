<?php
namespace EdusohoNet\Service\Util;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SimpleLog
{
    private $logDir;
    private $name;
    private $log;

    function __construct($name,$dir = "")
    {
        $this->name = $name;
        $dir = ($dir)?$dir:$name;
        $log = new Logger($name);
        if(!$this->logDir){
            $this->logDir = $this->_getLogDir($dir);
        }
        $log->pushHandler(new StreamHandler("{$this->logDir}/{$name}.log"));
        $this->log = $log;
    }

    public function addInfo($msg)
    {
        return $this->log->addInfo($msg);
    }

    public function addNotice($msg)
    {
        return $this->log->addNotice($msg);
    }

    public function addWarning($msg)
    {
        return $this->log->addWarning($msg);
    }

    public function addError($msg)
    {
        return $this->log->addError($msg);
    }

    public function setLogDir($logDir)
    {
        $this->logDir = $logDir;
    }

    public function getLogDir()
    {
        return $this->logDir;
    }

    private function _getLogDir($name)
    {
        $logDir = __DIR__."/../../../../app/logs";

        if (!is_dir($logDir)) {
            mkdir($logDir);
        }
        $logDir .= '/'.$name;

        if (!is_dir($logDir)) {
            mkdir($logDir);
        }

        return $logDir;
    }
}
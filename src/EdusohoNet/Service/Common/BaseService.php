<?php
namespace EdusohoNet\Service\Common;

use EdusohoNet\Service\Common\ServiceException;
use EdusohoNet\Service\Common\NotFoundException;
use EdusohoNet\Service\Common\AccessDeniedException;

abstract class BaseService
{

    private $error;

    protected function createService($name)
    {
        return $this->getKernel()->createService($name);
    }

    protected function createDao($name)
    {
        return $this->getKernel()->createDao($name);
    }

    protected function getKernel()
    {
        return ServiceKernel::instance();
    }

    public function getDispatcher()
    {
        return ServiceKernel::dispatcher();
    }

    protected function dispatchEvent($eventName, $subject)
    {
        if ($subject instanceof ServiceEvent) {
            $event = $subject;
        } else {
            $event = new ServiceEvent($subject);
        }
        $this->getDispatcher()->dispatch($eventName, $event);
    }

    public function getCurrentUser()
    {
        return $this->getKernel()->getCurrentUser();
    }

    protected function createServiceException($message = 'Service Exception', $code = 0)
    {
        return new ServiceException($message, $code);
    }

    protected function createAccessDeniedException($message = 'Access Denied', $code = 0)
    {
        return new AccessDeniedException($message, null, $code);
    }

    protected function createNotFoundException($message = 'Not Found', $code = 0)
    {
        return new NotFoundException($message, $code);
    }

    protected function getMillisecond() 
    {
        list($s1, $s2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function isTestEnv()
    {
        try {
            return $this->getKernel()->getParameter('env') == 'test';
        } catch (\Exception $e) {
            return false;
        }
        
    }

    protected function handleException(\Exception $e)
    {
        return array(
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
        );
    }
}
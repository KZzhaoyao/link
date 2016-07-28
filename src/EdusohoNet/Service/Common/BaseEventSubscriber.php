<?php
namespace EdusohoNet\Service\Common;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\DataDispatch;

class BaseEventSubscriber 
{
	protected function sPush($namespace, $key, $value = null)
	{
		DataDispatch::sPush($namespace, $key, $value);
	}

	protected function sRem($namespace, $key)
	{
		DataDispatch::sRem($namespace, $key);
	}

	protected function push($key, $value)
	{
		DataDispatch::push($key, $value);
	}

	protected function del($key)
	{
		DataDispatch::del($key);
	}

	protected function getRedis()
	{
		return $this->getKernel()->getRedis('master');
	}

	protected function getKernel()
    {
        return ServiceKernel::instance();
    }
}

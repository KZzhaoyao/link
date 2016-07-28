<?php
namespace EdusohoNet\Common;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\BeanstalkClient;


/* 
 * 消息格式：
 	'operate' => array(
		'namespace' => $namespace,	选填
		'key' => $key, 				必填
		'value' => $value,			选填
		'time' => $time 			必填
	)

	operate：操作类型
 *
 */
class DataDispatch 
{

	public static function sPush($namespace, $key, $value = null, $repeat = 0)
	{
		try{
			self::getRedis()->sAdd($namespace, $key);
			if(!empty($value)) {
				self::push($key, $value);
			}
		} catch(\Exception $e) {

			if($repeat<3) {
				DataDispatch::sPush($namespace, $key, $value, $repeat++);
				return;
			}

			BeanstalkClient::pushJob('dispatchAlarmAlias', 
				json_encode(
					array(
						'sPush' => array(
							'namespace' => $namespace,
							'key' => $key,
							'value' => $value,
						)
					)
				)
			);
		}
	}

	public static function sRem($namespace, $key, $repeat=0)
	{
		try{
			$result = self::getRedis()->sRem($namespace, $key);
			self::getRedis()->del($key);
		} catch(\Exception $e) {

			if($repeat<3) {
				DataDispatch::sRem($namespace, $key, $repeat++);
				return;
			}

			BeanstalkClient::pushJob('dispatchAlarmAlias', 
				json_encode(
					array(
						'sRem' => array(
							'namespace' => $namespace,
							'key' => $key,
						)
					)
				)
			);
		}
	}

	public static function push($key, $value, $ttl = 0, $repeat = 0)
	{
		try{
			if ($ttl > 0) {
            	$result = self::getRedis()->setex($key, $ttl, $value);
	        } else {
	            $result = self::getRedis()->set($key, $value);
	        }
		} catch(\Exception $e) {

			if($repeat<3) {
				DataDispatch::push($key, $value, $ttl, $repeat++);
				return;
			}

			BeanstalkClient::pushJob('dispatchAlarmAlias', 
				json_encode(
					array(
						'push' => array(
							'key' => $key,
							'value' => $value,
							'ttl' => $ttl
						)
					)
				)
			);
		}
	}

	public static function del($key, $repeat=0)
	{
		try{
			$result = self::getRedis()->del($key);
		} catch(\Exception $e) {

			if($repeat<3) {
				DataDispatch::del($key, $repeat++);
				return;
			}

			BeanstalkClient::pushJob('dispatchAlarmAlias', 
				json_encode(
					array(
						'del' => array(
							'key' => $key,
						)
					)
				)
			);
		}
	}

	protected static function getMillisecond() 
    {
        list($s1, $s2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
    } 

	private static function getRedis()
	{
		return self::getKernel()->getRedis('master');
	}

	private static function getKernel()
    {
        return ServiceKernel::instance();
    }
}

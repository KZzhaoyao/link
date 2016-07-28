<?php
namespace EdusohoNet\Common;
// Hopefully you're using Composer autoloading.

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Pheanstalk\Job;
use Pheanstalk\Exception\ServerException;

class BeanstalkClient 
{
	// ----------------------------------------
	// producer (queues jobs)
	public static function getJob($tubeAlias)
	{
		$pheanstalk=self::getPheanstalkInstall($tubeAlias);
		$job = $pheanstalk->reserveFromTube(self::getTube($tubeAlias));
		$pheanstalk->bury($job);
		return $job;
	}

	public static function getJobAndDelete($tubeAlias)
	{
		$pheanstalk=self::getPheanstalkInstall($tubeAlias);
		$job = $pheanstalk->reserveFromTube(self::getTube($tubeAlias));
		$pheanstalk->delete($job);
		return $job;
	}

	/***
	*  put($data,$priority=32,$delayedseconds ,$allowWorkTime= 3 * 60 * 60 )
	*  	*.priority:Jobs with smaller priority values will be
	*	  scheduled before jobs with larger priorities. 2*32 default	
	*	*.delayedseconds: delay seconds to release
	*	*.allowWorkTime: means time to run,
	*/
	public static function pushJob($tubeAlias,$data, $tubeSettings = array())
	{
		$priority = array_key_exists('priority', $tubeSettings) ? $tubeSettings['priority'] : PheanstalkInterface::DEFAULT_PRIORITY;
		$delay = array_key_exists('delay', $tubeSettings) ? $tubeSettings['delay'] : PheanstalkInterface::DEFAULT_DELAY;
		$ttr = array_key_exists('ttr', $tubeSettings) ? $tubeSettings['ttr'] : PheanstalkInterface::DEFAULT_TTR;
		try{
			self::getPheanstalkInstall($tubeAlias)->putInTube(
				self::getTube($tubeAlias),
				$data,
				$priority,
				$delay,
				$ttr);
		}catch(\Exception $e){
			return false;
		}
		return true;
	}

	public static function deleteJob($tubeAlias, $id){
		self::getPheanstalkInstall($tubeAlias)->delete(new Job($id,''));
	}

	/**
	*	publish job with delay time.
	*
	*
	*/
	public static function pushJobDelay($tubeAlias,$data,$delay)
	{
		try{
			self::getPheanstalkInstall($tubeAlias)->putInTube(
				self::getTube($tubeAlias),
				$data,
				PheanstalkInterface::DEFAULT_PRIORITY,
				$delay,
				PheanstalkInterface::DEFAULT_TTR);
		}catch(\Exception $e){
			return false;
		}
		return true;
	}

	public static function jobStats($tubeAlias){
		return self::getPheanstalkInstall($tubeAlias)->stats();
	}

	public static function jobNum($tubeAlias){
		return self::readyJobNum($tubeAlias)+self::processingJobNum($tubeAlias);
	}

	public static function readyJobNum($tubeAlias){
		try{
			$stats = self::jobStats($tubeAlias);
			return intval($stats['current-jobs-ready']);
		}catch(ServerException $e){

		}
		return 0;
	}

	public static function processingJobNum($tubeAlias){
		try{
			$stats =  self::jobStats($tubeAlias);
			return intval($stats['current-jobs-buried']);
		}catch(ServerException $e){

		}
		return 0;
	}

	public static function processingTubeJobNum($tubeAlias){
		try{
			$stats= self::getPheanstalkInstall($tubeAlias)
				->statsTube(self::getTube($tubeAlias));
			return $stats['current-jobs-buried'];
		}catch(ServerException $e){

		}
		return 0;
	}

	public static function getProcessingJob($tubeAlias){
		try{
			return self::getPheanstalkInstall($tubeAlias)->peekBuried(self::getTube($tubeAlias));
		}catch(ServerException $e){

		}
		return null;
	}

	public static function releasedAllProcessingJob($tubeAlias){
		try{
			while(1){
				$job=self::getProcessingJob($tubeAlias);
				if($job!=null){
					self::getPheanstalkInstall($tubeAlias)->kickJob($job);
				}else{
					return ;
				}
			}
		}catch(ServerException $e){

		}
	}

	public static function getConfig()
	{
		return include __DIR__ . '/../../../app/config.php';
	}

	public static function getTube($tubeAlias)
	{
		$config = self::getConfig();
		return $config['beanstalkd'][$tubeAlias]['tube'];
	}

	public static function getPheanstalkInstall($queue){
		$config = self::getConfig();
		return new Pheanstalk($config['beanstalkd'][$queue]['host'], $config['beanstalkd'][$queue]['port']);
	}
}
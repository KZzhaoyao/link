<?php

namespace EdusohoNet\Service\Util;

class LiveProviders
{
	protected static $liveProviderData = array(
		'1'=>'vhall',
		'2'=>'soooner',
		'3'=>'sanmang',
		'4'=>'gensee',
	);

	public static function getCode($providerName)
	{
		if(empty($providerName)){
			return '';
		}
		foreach (self::$liveProviderData as $key => $value) {
			if($providerName == $value){
				return $key;
			}
		}
		return '';
	}

	public static function getProviderName($code)
	{
		if(empty($code) || !array_key_exists($code, self::$liveProviderData)){
			return '';
		}
		return self::$liveProviderData[$code];
	}
}
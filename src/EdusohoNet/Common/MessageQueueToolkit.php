<?php
namespace EdusohoNet\Common;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MessageQueueToolkit
{
    public static function createJob($module, $data)
    {
        $result = array(
            'fromServer' => self::getServerName(),
            'uniqId' => self::getUniqId(),
            'module' => $module,
            'contents' => $data,
            'createdTime' => time(),
            );
        return $result;
    }

    public static function getMessageQueueLogger()
    {
        $logPath = __DIR__ . '/../../../var/logs/message_queue.log';
        $logger = new Logger('messageQueue');
        $logger->pushHandler(new StreamHandler($logPath));
        return $logger;
    }

    public static function messageQueueInfo($jobData, $tube, $process = 'Start')
    {
        self::getMessageQueueLogger()->info("jobData: {$jobData}");
        $jobData = json_decode($jobData, true);
        $contents = json_encode($jobData['contents']);
        self::getMessageQueueLogger()->info("Process: {$process},Tube: {$tube}, From: {$jobData['fromServer']}, UniquId: {$jobData['uniqId']}, Module: {$jobData['module']}, Contents: {$contents}, CreatedTime: {$jobData['createdTime']}");
    }

    private static function getUniqId()
    {
        return uniqid(md5(self::getServerName()));
    }

    private static function getServerName()
    {
        if (array_key_exists('SERVER_NAME', $_SERVER)) {
            return $_SERVER['SERVER_NAME'];
        }
        return 'unknown';
    }
}
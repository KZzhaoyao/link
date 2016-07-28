<?php
namespace EdusohoNet\Service\Common;

use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Service\Common\DaoEvent;
use PDO;

abstract class BaseDao
{
    protected $connection;

    protected $dispatcher;

    private $error;

    private static $cachedObjects = array();

    public function getTable()
    {
        return $this->table;
    }

    public function getConnection ()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    public function setDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setError($error){
        $this -> error = $error;
    }

    public function getError(){
        return $this -> error;
    }

    protected function dispatchEvent($eventName, $subject)
    {
        if ($subject instanceof DaoEvent) {
            $daoEvent = $subject;
        } else {
            $daoEvent = new DaoEvent($subject);
        }
        $this->getDispatcher()->dispatch($eventName, $daoEvent);
    }

    protected function createDaoException($message = null, $code = 0) 
    {
        return new DaoException($message, $code);
    }

    protected function createDynamicQueryBuilder($conditions)
    {
        return new DynamicQueryBuilder($this->getConnection(), $conditions);
    }

    protected function createSerializer()
    {
        if (!isset(self::$cachedObjects['field_serializer'])) {
            self::$cachedObjects['field_serializer'] = new FieldSerializer();
        }
        return self::$cachedObjects['field_serializer'];
    }

    protected function filterStartLimit(&$start, &$limit)
    {
       $start = (int) $start;
       $limit = (int) $limit; 
    }

    protected function deleteCache($key)
    {
        if (is_array($key)) {
            $group = $key[0];
            $key = $key[1];
        } else {
            $group = 'default';
        }

        ServiceKernel::instance()->getRedis($group)->delete($key);
    }

    protected function callCache($key, $callback, $ttl = 864000)
    {
        if (is_array($key)) {
            $group = $key[0];
            $key = $key[1];
        } else {
            $group = 'default';
        }

        $redis = ServiceKernel::instance()->getRedisSlave($group);

        $data = $redis->get($key);
        if ($data) {
            return $data;
        }

        $data = $callback();

        $redis = ServiceKernel::instance()->getRedis($group);
        if ($ttl && $ttl > 0) {
            $redis->setex($key, $ttl, $data);
        } else {
            $redis->set($key, $data);
        }

        return $data;
    }

    protected function getMillisecond() 
    {
        list($s1, $s2) = explode(' ', microtime()); 
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); 
    } 

}
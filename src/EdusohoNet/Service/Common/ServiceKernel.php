<?php
namespace EdusohoNet\Service\Common;

use EdusohoNet\Common\RedisPool;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class ServiceKernel
{

    private static $_instance;

    private static $_dispatcher;

    protected $environment;

    protected $_moduleDirectories = array();

    protected $_moduleConfig = array();

    protected $debug;

    protected $booted;

    protected $currentUser;

    protected $connection;

    protected $pool = array();

    protected $directories = array();

    public static function create($environment, $debug)
    {
        if (self::$_instance) {
            return self::$_instance;
        }

        $instance = new self();
        $instance->environment = $environment;
        $instance->debug = (Boolean) $debug;

        $instance->registerModuleDirectory(realpath(__DIR__.'/../../../'));

        self::$_instance = $instance;

        return $instance;
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            throw new \RuntimeException('ServiceKernel未实例化');
        }
        self::$_instance->boot();

        return self::$_instance;
    }

    public static function dispatcher()
    {
        if (self::$_dispatcher) {
            return self::$_dispatcher;
        }

        self::$_dispatcher = new EventDispatcher();

        return self::$_dispatcher;
    }

    public function getRootDir()
    {
        return realpath(__DIR__).'/../../../..';
    }

    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        $this->booted = true;

        $moduleConfigCacheFile = $this->getRootDir().'/app/cache/'.$this->environment.'/modules_config.php';

        if (file_exists($moduleConfigCacheFile)) {
            $this->_moduleConfig = include $moduleConfigCacheFile;
        } else {
            $finder = new Finder();
            $finder->directories()->depth('== 0');

            foreach ($this->_moduleDirectories as $dir) {
                if (glob($dir.'/*/Service', GLOB_ONLYDIR)) {
                    $finder->in($dir.'/*/Service');
                }
            }

            foreach ($finder as $dir) {
                $filepath = $dir->getRealPath().'/module_config.php';

                if (file_exists($filepath)) {
                    $this->_moduleConfig = array_merge_recursive($this->_moduleConfig, include $filepath);
                }
            }

            $this->_moduleConfig = array_merge_recursive($this->_moduleConfig, include $this->getRootDir().'/src/EdusohoNet/Listerner/module_config.php');

            if (!$this->debug) {
                $cache = "<?php \nreturn ".var_export($this->_moduleConfig, true).';';
                file_put_contents($moduleConfigCacheFile, $cache);
            }
        }

        $subscribers = empty($this->_moduleConfig['event_subscriber']) ? array() : $this->_moduleConfig['event_subscriber'];

        foreach ($subscribers as $subscriber) {
            $this->dispatcher()->addSubscriber(new $subscriber());
        }
    }

    public function setParameterBag($parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getParameter($name)
    {
        if (is_null($this->parameterBag)) {
            throw new \RuntimeException('尚未初始化ParameterBag');
        }
        if (!isset($this->parameterBag[$name])) {
            throw new \RuntimeException("{$name}的配置不存在");
        }

        return $this->parameterBag[$name];
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    public function getCurrentUser()
    {
        if (is_null($this->currentUser)) {
            throw new \RuntimeException('尚未初始化CurrentUser');
        }

        return $this->currentUser;
    }

    public function setDirectories($directories = array())
    {
        $this->directories = $directories;
    }

    public function getDirectory($name)
    {
        if (empty($this->directories[$name])) {
            throw new \RuntimeException("`{$name}`目录路径尚未定义。");
        }

        return $this->directories[$name];
    }

    public function registerModuleDirectory($dir)
    {
        $this->_moduleDirectories[] = $dir;
    }

    public function getConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }

        $config = $this->getParameter('database');
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($config);

        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function getRedis($group = 'default')
    {
        return $this->getRedisPool()->getRedis($group);
    }

    public function getRedisSlave($group = 'default')
    {
        return $this->getRedisPool()->getRedisSlave($group);
    }

    public function createService($name)
    {
        if (empty($this->pool[$name])) {
            $namespace = substr(__NAMESPACE__, 0, -strlen('Common')-1);
            list($module, $className) = explode('.', $name);
            $class = $namespace.'\\'.$module.'\\Impl\\'.$className.'Impl';
            $this->pool[$name] = new $class();
        }

        return $this->pool[$name];
    }

    public function rpc($entrypoint, $service)
    {
        $key = "_rpc.{$entrypoint}.{$service}";
        if (!empty($this->pool[$key])) {
            return $this->pool[$key];
        }

        $config = $this->getParameter('rpc');

        if (empty($config['entry_points'][$entrypoint])) {
            throw new \RuntimeException("RPC entry point: {$entrypoint} is not found.");
        }

        $url = "{$config['entry_points'][$entrypoint]}?service={$service}";

        $rpc = new \Yar_Client($url);
        $rpc->SetOpt(YAR_OPT_TIMEOUT, $config['timeout']);
        $rpc->SetOpt(YAR_OPT_CONNECT_TIMEOUT, $config['connect_timeout']);

        return $this->pool[$key] = $rpc;
    }

    public function createDao($name)
    {
        if (empty($this->pool[$name])) {
            $namespace = substr(__NAMESPACE__, 0, -strlen('Common')-1);
            list($module, $className) = explode('.', $name);
            $class = $namespace.'\\'.$module.'\\Dao\\Impl\\'.$className.'Impl';
            $dao = new $class();
            $dao->setConnection($this->getConnection());
            $dao->setDispatcher(self::dispatcher());
            $this->pool[$name] = $dao;
        }

        return $this->pool[$name];
    }

    protected function getRedisPool()
    {
        if (isset($this->redisPool)) {
            return $this->redisPool;
        }

        $this->redisPool = RedisPool::init($this->getParameter('redis'));

        return $this->redisPool;
    }
}

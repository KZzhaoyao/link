<?php

namespace EdusohoNet\Service\Common;

use EdusohoNet\Service\Common\ServiceKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand;

class BaseServiceTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $isDatabaseCreated = false;

    protected static $serviceKernel = null;

    protected function getCurrentUser()
    {
        return static::$serviceKernel->getCurrentUser();
    }


    public static function setUpBeforeClass()
    {
        // static::$kernel = static::createKernel();
        // static::$kernel->boot();
        //self::$serviceKernel->getRedis()->flushAll();
    }

    /**
     * 每个testXXX执行之前，都会执行此函数，净化数据库。
     * 
     * NOTE: 如果数据库已创建，那么执行清表操作，不重建。
     */
    protected function setServiceKernel()
    {
        if (static::$serviceKernel) {
            return ;
        }

        // $kernel = new \AppKernel('test', false);
        $config = include __DIR__ . '/../../../../app/config_test.php';
        $kernel = ServiceKernel::create('test', true);
        $kernel->setParameterBag($config);
        $kernel->setDirectories(array(
            'cache' => dirname(__DIR__) . '/var/cache',
            'log' => dirname(__DIR__) . '/var/logs',
        ));
        $kernel->boot();
        Request::enableHttpMethodParameterOverride();
        // $request = Request::createFromGlobals();

        $serviceKernel = $kernel;//ServiceKernel::create($kernel->getEnvironment(), $kernel->isDebug());
        // $serviceKernel->setParameterBag($kernel->getContainer()->getParameterBag());
        $connection = $kernel->getConnection();
        $serviceKernel->setConnection(new TestCaseConnection($connection));
        
        $currentUser=array(
            'id' => 1,
            'nickname' => 'admin',
            'email' => 'admin@admin.com',
            'password'=>'admin',
            'currentIp' => '127.0.0.1',
            'loginIp' => '127.0.0.1',
            'roles' => array('ROLE_USER','ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_TEACHER')
        );
        $serviceKernel->setCurrentUser($currentUser);

        static::$serviceKernel = $serviceKernel;
    }

    public function getServiceKernel()
    {
        return static::$serviceKernel;
    }

    public function setUp()
    {
        $this->setServiceKernel();

        if (!static::$isDatabaseCreated) {
            $this->createAppDatabase();
            static::$isDatabaseCreated = true;
            $this->emptyAppDatabase(true);
        } else {
            $this->emptyAppDatabase(false);
        }
        $this->getRedis()->select(2);
        //$this->getRedis()->flushAll();
        $this->getRedis()->flushDB();
    }

    public function tearDown()
    {
    
    }

    protected function createAppDatabase()
    {
        // 执行数据库的migrate脚本
        // $application = new Application();
        // TODO  这里应该可以自动构建数据库
        // $application->add(new MigrationsMigrateDoctrineCommand());
        // $command = $application->find('doctrine:migrations:migrate');
        // $commandTester = new CommandTester($command);
        // $commandTester->execute(
        //     array('command' => $command->getName()),
        //     array('interactive' => false)
        // );
        // passthru('/var/www/api.edusoho.net/vendor/bin/phinx migrate');
    }

    protected function emptyAppDatabase($emptyAll = true)
    {
        $connection = static::$serviceKernel->getConnection();

        if ($emptyAll) {
            $tableNames = $connection->getSchemaManager()->listTableNames();
        } else {
            $tableNames = $connection->getInsertedTables();
            $tableNames = array_unique($tableNames);
        }

        $sql = '';
        foreach ($tableNames as $tableName) {
            if ($tableName == 'migration_versions' || $tableName == 'phinxlog') {
                continue;
            }
            $sql .= "TRUNCATE {$tableName};";
        }
        if (!empty($sql)) {
            $connection->exec($sql);
            $connection->resetInsertedTables();
        }
    }

    protected function getRedis()
    {
        return static::$serviceKernel->getRedis();
    }
}

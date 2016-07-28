<?php
namespace Worker;

use Footstones\Plumber\IWorker;
use Psr\Log\LoggerInterface;
use EdusohoNet\Service\Common\ServiceKernel;

abstract class AbstractWorker implements IWorker
{
    protected $logger = null;

    public function execute($job)
    {
        try {
            if(empty($job) || !array_key_exists("contents", $job['body'])) {
                $this->logger->warning("job is invalid. ", $job);
                return IWorker::FINISH;
            }

            $this->checkConnection($job);
            return $this->process($job);
        } catch (\Exception $e) {
            $this->logger->error("job throw exception: {$e->getMessage()}", $job);
            return IWorker::FINISH;
        }
    }

    abstract protected function process($data);

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function checkConnection($job)
    {
        $conn = ServiceKernel::instance()->getConnection();
        if ($conn->ping() === false) {
            $this->logger->info("mysql reconncetion. ", $job);
            $conn->close();
            $conn->connect();
        }
    }
}
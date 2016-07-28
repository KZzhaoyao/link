<?php
namespace Worker;

use Footstones\Plumber\IWorker;
use Psr\Log\LoggerInterface;
use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\ArrayToolkit;
use QiQiuYun\LiveProvider\LiveProviderFactory;

class LiveroomEventWorker implements IWorker
{
    protected $logger = null;

    public function execute($job)
    {
        try {
            $this->checkConnection($job);

            $event = $job['body'];

            switch ($event['type']) {
                case 'ticket_produced':
                    $this->logger && $this->logger->info("Ticket {$event['data']['no']} produced.", $event['data']);
                    break;

                case 'ticket_consumed':
                    $this->logger && $this->logger->info("Ticket {$event['data']['no']} consumed.", $event['data']);

                    $room = $this->getLiveService()->getRoom($event['data']['roomId']);

                    $provider = $this->getLiveService()->getProviderByUserIdAndName($room['userId'], $room['provider']);

                    $options = [
                        'accessKey' => $provider['accessKey'],
                        'secretKey' => $provider['secretKey'],
                        'password' => $provider['password'],
                    ];

                    $provider = LiveProviderFactory::create($provider['provider'], $options);

                    $onlineNum = $provider->getRoomOnlineNum($room);

                    if ($onlineNum > $room['onLineNum']) {
                        $this->getLiveService()->updateRoom($room['id'], ['onLineNum' => $onlineNum]);
                    }

                    $maxOnlieNum = $provider->getRoomMaxOnlieNum($room);

                    $this->logger && $this->logger->info("Room #{$room['id']}, pre online: {$room['onLineNum']}, current online: {$onlineNum}, max online: {$maxOnlieNum}");

                    break;
                
                default:
                    $this->logger && $this->logger->warning("unknow liveroom event", $event);
                    break;
            }

            return IWorker::FINISH;

        } catch (\Exception $e) {
            $this->logger && $this->logger->error("job throw exception: {$e->getMessage()}", $job);
            return IWorker::FINISH;
        }
    }

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

    protected function getLiveService()
    {
        return ServiceKernel::instance()->createService('Live.LiveService');
    }
}
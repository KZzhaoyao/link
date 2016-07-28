<?php
namespace Worker;

use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;

class LiveRoomUpdateWorker extends AbstractWorker
{
    public function process($job)
    {
        try {
            $data = $job["body"]["contents"];
            $this->logger->info("job #{$job['id']} is ready. ", $job);

            if(!ArrayToolkit::requireds($data, array("id", "fields"))){
                $this->logger->error("required fields error, jobId is {$job['id']}", $job);
                return IWorker::FINISH;
            }

            if(array_key_exists("id", $data) && array_key_exists("fields", $data)){
                $fields = $data["fields"];
                $id = $data["id"];
                $this->getLiveRoomService()->updateLiveRoom($id, $fields);
            }
            return IWorker::FINISH;
        } catch (\Exception $e) {
            $this->logger->error("job #{$job['id']} is error. exception: {$e->getMessage()}", $job);
            return IWorker::BURY;
        }
    }

    protected function getLiveRoomService()
    {
        return ServiceKernel::instance()->createService('Live.LiveRoomService');
    }

}

<?php
namespace Worker;

use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;

class AddSmsWorker extends AbstractWorker
{
    public function process($job)
    {
        try {
            $data = $job["body"]["contents"];
            $data['status'] = isset($data['status'])?$data['status']:'submited';

            $data['gatewayTime'] = time();

            if(!ArrayToolkit::requireds($data, array("status", "userId", "mobile", "message", "num", "category", "batchId"))) {
                $this->logger->error("job #{$job['id']} required fields error. ", $job);
                return IWorker::FINISH;
            }

            $this->logger->info("job #{$job['id']} data.",$data);

            $this->initCurrentUser($data['userId']);
            $result = $this->getSmsService()->addSmsSended($data);
            $this->logger->info("job #{$job['id']} addSmsSended.",$result);

            //TODO getSmsSenderStats

            return IWorker::FINISH;
        } catch (\Exception $e) {
            $this->logger->error("job #{$job['id']} is error. exception: {$e->getMessage()}", $job);
            return IWorker::BURY;
        }


    }

    protected function initCurrentUser($userId)
    {
        $user = $this->getUserService()->getUser($userId);
        ServiceKernel::instance()->setCurrentUser($user);
    }

    protected function getSmsService()
    {
        return ServiceKernel::instance()->createService('Sms.SmsService');
    }

    protected function getUserService()
    {
        return ServiceKernel::instance()->createService('User.UserService');
    }

}

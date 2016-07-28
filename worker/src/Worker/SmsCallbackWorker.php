<?php
namespace Worker;

use EdusohoNet\Service\Common\ServiceKernel;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;

class SmsCallbackWorker extends AbstractWorker
{
    public function process($job)
    {
        try {
            $data = $job["body"]["contents"];

            if (!ArrayToolkit::requireds($data, array("status", "batchId", "mobile"))) {
                $this->logger->error("job #{$job['id']} required fields error. ", $job);

                return IWorker::FINISH;
            }

            $reason = isset($data['reason'])?$data['reason']:'';

            $this->getSmsService()->updateSendDetailStatus($data['batchId'], $data['mobile'], $data['status'], $reason);

            return IWorker::FINISH;
        } catch (\Exception $e) {
            $this->logger->error("job #{$job['id']} is error. exception: {$e->getMessage()}", $job);

            return IWorker::BURY;
        }
    }

    protected function getSmsService()
    {
        return ServiceKernel::instance()->createService('Sms.SmsService');
    }
}

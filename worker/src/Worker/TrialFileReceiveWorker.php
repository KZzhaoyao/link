<?php
namespace Worker;

use EdusohoNet\Common\MessageQueueToolkit;
use EdusohoNet\Common\BeanstalkClient;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;
use EdusohoNet\Service\Common\ServiceKernel;

class TrialFileReceiveWorker extends AbstractWorker
{
    public function process($data)
    {
        try {
            $dataArr = $data['body']['contents'];
            if (!ArrayToolkit::requireds($dataArr, array('userId', 'fileId', 'originalFileName', 'quality'))) {
                $this->logger->error("required fields error, jobId is {$data['id']}", $data);
                return IWorker::FINISH;
            }

            $this->logger->info("receive trial file start. jobId is {$data['id']}", $data);

            $params = ArrayToolkit::parts($dataArr, array('userId', 'fileId', 'originalFileName', 'quality'));
            if (!$this->getTrialFileService()->isExist($params['userId'], $params['fileId'], $params['quality'])) {
                $this->getTrialFileService()->add($params);

                $jobData = MessageQueueToolkit::createJob('trialFileRenameTube', $params);
                BeanstalkClient::pushJob("trialFileRenameTube", json_encode($jobData), ['ttr' => 1200, 'delay' => 3600]);
                $this->logger->info("push trial file rename job success. jobId is {$data['id']}", $data);
            };

            return IWorker::FINISH;
        } catch (\Exception $e) {
            $this->logger->error("job #{$data['id']} is error. exception: {$e->getMessage()}", $data);

            return IWorker::FINISH;
        }
    }

    protected function getTrialFileService()
    {
        return ServiceKernel::instance()->createService('TrialFile.TrialFileService');
    }
}

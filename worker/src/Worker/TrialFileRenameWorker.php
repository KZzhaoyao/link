<?php
namespace Worker;

use EdusohoNet\Common\MessageQueueToolkit;
use EdusohoNet\Common\BeanstalkClient;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;
use EdusohoNet\Service\Common\ServiceKernel;

class TrialFileRenameWorker extends AbstractWorker
{
    const DELAY_RATIO = 20;
    public function process($data)
    {
        try {
            $dataArr = $data['body']['contents'];

            if (!ArrayToolkit::requireds($dataArr, array('userId', 'fileId', 'originalFileName', 'quality'))) {
                $this->logger->error("required fields error, jobId is {$data['id']}", $data);
                return IWorker::FINISH;
            }

            $this->logger->info("rename trial file start. jobId is {$data['id']}", $data);
            $params = ArrayToolkit::parts($dataArr, array('userId', 'fileId', 'originalFileName', 'quality'));
            $this->getTrialFileService()->renameTrialFiles($params);

            return IWorker::FINISH;
        } catch (\Exception $e) {
            $this->logger->error("job #{$data['id']} is error. exception: {$e->getMessage()}", $data);
            if (array_key_exists('retry', $data['body']) && $data['body']['retry'] > 2) {
                return IWorker::BURY;
            }
            $retry = array_key_exists('retry', $data['body']) ? $data['body']['retry'] : 0;
            return array('code' => IWorker::RETRY, 'delay'=> ($retry + 1) * self::DELAY_RATIO);
        }
    }

    protected function getTrialFileService()
    {
        return ServiceKernel::instance()->createService('TrialFile.TrialFileService');
    }

}

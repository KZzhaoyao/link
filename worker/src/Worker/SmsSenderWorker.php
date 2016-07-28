<?php
namespace Worker;

use EdusohoNet\Common\MessageQueueToolkit;
use EdusohoNet\Common\BeanstalkClient;
use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;
use Worker\SmsProvider\SmsProviderFactory;
use EdusohoNet\Service\Common\ServiceKernel;

class SmsSenderWorker extends AbstractWorker
{
    private $data;

    public function process($data)
    {
        $this->data = $data;
        try {
            $dataArr = $data['body']['contents'];

            if (!ArrayToolkit::requireds($dataArr, array('userId', 'provider', 'mobile', 'sendData', 'callbackUrl', 'category'))) {
                $this->logger->error("required fields error, jobId is {$data['id']}", $data);
                return IWorker::FINISH;
            }

            if(!$this->_setSendTemplate()){
                return IWorker::FINISH;
            }

            $this->sendSmsBySlice();

            $this->logger->info("sms send start. jobId is {$data['id']}", $data);

            return IWorker::FINISH;

        } catch (\Exception $e) {
            $this->logger->error("job #{$data['id']} is error. exception: {$e->getMessage()}", $data);
            return IWorker::FINISH;
        }
    }

    private function _setSendTemplate()
    {
        $data = $this->data;
        $contents = $data['body']['contents'];
        $sendData = $contents['sendData'];
        $this->initCurrentUser($contents['userId']);
        switch ($sendData['type']) {
            case 'single':
                $singleSendTemplate = $this->_getSingleSendTemplate();
                if(!$singleSendTemplate){
                    return false;
                }
                $sendTemplate = $this->getSmsService()->getSmsProvider()->getSendTemplate($singleSendTemplate,$this->data['body']['contents']['category']);
                $this->data['body']['contents']['sendTemplate'] = $sendTemplate;
                return true;
            case 'batch':
                $batchSendTemplate = $this->_getBatchSendTemplate();
                if(!$batchSendTemplate){
                    return false;
                }
                $sendTemplate = $this->getSmsService()->getSmsProvider()->getSendTemplate($batchSendTemplate,$this->data['body']['contents']['category']);
                $this->data['body']['contents']['sendTemplate'] = $sendTemplate;
                return true;
            default:
                $this->logger->error("非法方法:{$sendData['type']}获取模板", $data);
                return false;
        }
    }

    private function _getSingleSendTemplate()
    {
        $data = $this->data;
        $contents = $data['body']['contents'];
        $sendData = $contents['sendData'];

        $smsContent = $this->getSmsService()->getSmsProvider()->getSmsContent($sendData['parameters'], $contents['category']);
        if (!$smsContent || !$smsContent['content']) {
            $this->logger->error("获取短信内容时出错".$this->getSmsService()->getSmsProvider()->getError(),$data);
            return false;
        }

        return $smsContent['templates'];
    }

    private function _getBatchSendTemplate()
    {
        $data = $this->data;
        $contents = $data['body']['contents'];
        $sendData = $contents['sendData'];
        $params = $sendData['parameters'];

        $smsData = $this->_handleBatchData($this->_getSendBatchData($params));
        $this->data['body']['contents']['mobile'] = $smsData['mobile'];
        $this->data['body']['contents']['category'] = $smsData['category'];
        $this->data['body']['contents']['description'] = isset($smsData['description'])?$smsData['description']:'';

        if (!$smsData) {
            return false;
        }

        $smsContent = $this->getSmsService()->getSmsProvider()->getSmsContent($smsData['parameters'], $smsData['category']);

        if (!$smsContent['content']) {
            $this->logger->error("获取短信内容时出错:".$this->getSmsService()->getSmsProvider()->getError(),$data);
            return false;
        }

        return $smsContent['templates'];
    }

    private function _handleBatchData($batchData)
    {
        if (!$batchData || !is_array($batchData)) {
            $this->logger->error("群发未能成功获取短信数据",array(
                'batchData' => $batchData,
                'job' => $this->data));
            return false;
        }
        $mobileArr = array();
        foreach ($batchData as $data) {
            $mobileArr[] = $data['mobile'];
        }
        $mobileArr = array_filter($mobileArr);
        if (!$mobileArr) {
            $this->logger->error("群发的手机号码不能为空",array(
                'batchData' => $batchData,
                'job' => $this->data));
            return false;
        }
        $targetData = $batchData[0];
        $targetData['mobile'] = implode(',', $mobileArr);
        return $targetData;
    }

    private function _getSendBatchData($params)
    {
        $batchData = array();
        foreach ($params['callbackUrls'] as $callback) {
            try {
                list($response, $httpCode) = $this->getSmsService()->sendRequest('GET', $callback, array());
                $callData = json_decode($response, true);
                if (isset($callData['error'])) {
                    throw new \Exception($callData['error']);
                }
                if (!$callData) {
                    throw new \Exception("未获取正确数据");
                }
                $batchData[] = $callData;
            } catch (\Exception $e) {
                $this->logger->error($callback . ":获取数据失败",$this->data);
            }
        }
        return $batchData;
    }

    private function initCurrentUser($userId)
    {
        $user = $this->getUserService()->getUser($userId);
        ServiceKernel::instance()->setCurrentUser($user);
    }

    private function sendSmsBySlice()
    {
        $data = $this->data;
        $dataArr = $data['body']['contents'];
        $smsProviderFactory = new SmsProviderFactory($this->logger);
        $smsProvider = $smsProviderFactory->create($dataArr['provider']);

        $sliceNum = $this->getSliceNum();

        $mobiles = explode(',', $dataArr['mobile']);
        $mobileArr = array_chunk($mobiles, $sliceNum);

        $sendedSn = $this->makeSendedSn();

        foreach ($mobileArr as $chunkMobile) {
            $chunkDataArr = $dataArr;
            $chunkDataArr['mobile'] = implode(',', $chunkMobile);
            $chunkDataArr['message'] = $dataArr['sendTemplate']['message'];
            list($result, $code) = $smsProvider->send($chunkDataArr);
            $smsSended = $smsProvider->getSendedSmsParams($chunkDataArr);
            $this->logger->info("sms sended. result is {$result}, code is {$code}, jobId is {$data['id']} ", array(
                'data' => $data,
                'chunkData' => $chunkDataArr,
            ));
            $smsSended['batchId'] = $code;
            if ($result == "success") {
                $smsSended['status'] = "submited";
            } else {
                $smsSended['status'] = "failed";
                $smsSended['reason'] = $result;
            }
            $smsSended['sendedSn'] = $sendedSn;
            $jobData = MessageQueueToolkit::createJob('rootAddSmsSendTube', $smsSended);
            BeanstalkClient::pushJob("rootAddSmsSendTube", json_encode($jobData));
            $this->logger->info("push smssended job success. jobId is {$data['id']}", $data);
        }
    }

    private function makeSendedSn()
    {
        return 'S' . date('YmdHis') . rand(10000, 99999);
    }

    private function getSliceNum()
    {
        try {
            $config = ServiceKernel::instance()->getParameter('sms');
            $sliceNum = $config['sliceNum'];
        } catch (\Exception $e) {
            $sliceNum = 100;
        }
        return $sliceNum;
    }

    private function getSmsService()
    {
        return ServiceKernel::instance()->createService('Sms.SmsService');
    }

    private function getUserService()
    {
        return ServiceKernel::instance()->createService('User.UserService');
    }
}

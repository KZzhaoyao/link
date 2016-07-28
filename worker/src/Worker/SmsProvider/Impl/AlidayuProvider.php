<?php
namespace Worker\SmsProvider\Impl;

use Worker\SmsProvider\AbstractSmsProvider;
use EdusohoNet\Service\Util\AlidayuHelper;

class AlidayuProvider extends AbstractSmsProvider
{
    protected $userAgent = 'EduSoho SMS Client 1.0';

    protected $connectTimeout = 15;

    protected $timeout = 15;

    protected $logger;

    private $appkey = '23237992';
    private $appSecrect = '4f5e5558b7253e09e8cee3c2629df4ba';

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function send($dataArr)
    {
        $uid = uniqid('ali', true);
        $dataArr['extend'] = $uid;
        $result = $this->sendTemplate($dataArr);
        return $result;
    }

    public function getBalance()
    {
        $params = array("username" => $this->username,"passwordmd5" => $this->passwordmd5);

        return $this->sendRequest("GET", "/getBalance.asp", $params);
    }

    public function getExtensionNumFormat()
    {
        return "1069%s1261";
    }

    private function sendTemplate($dataArr)
    {
        $smsSend = $this->_getSmsSend($dataArr);
        $alidayuHelper = new AlidayuHelper($this->appkey,$this->appSecrect);
        $sendResult = $alidayuHelper->sendSms($smsSend);
        if($sendResult->result->success){
            return array('success', $dataArr['extend']);
        }
        return array($sendResult->result->msg, $dataArr['extend']);
    }

    private function _getSmsSend($dataArr)
    {
        $sendTemplate = $dataArr['sendTemplate'];
        $alidayuTemplates = $sendTemplate['alidayu'];
        $smsSend = array(
            'extend' => $dataArr['extend'],
            'smsType' => 'normal',
            'smsFreeSignName' => $alidayuTemplates['name'],
            'smsParam' => $alidayuTemplates['template'],
            'recNum' => $dataArr['mobile'],
            'smsTemplateCode' => $alidayuTemplates['tpl'],
        );
        return $smsSend;
    }
}
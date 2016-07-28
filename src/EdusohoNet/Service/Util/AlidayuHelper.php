<?php
namespace EdusohoNet\Service\Util;

class AlidayuHelper
{
    private $client;
    private $req;
    private $consumeReq;
    private $confirmReq;

    function __construct($appkey, $secret)
    {
        include_once(__DIR__."/../../../../vendor/alidayu/TopSdk.php");
        $this->client = new \TopClient($appkey, $secret);
    }

    public function sendSms($params)
    {
        $allowParams = array('extend','smsType','smsFreeSignName','smsParam','recNum','smsTemplateCode');
        $this->req = new \AlibabaAliqinFcSmsNumSendRequest();
        foreach($params as $key => $param){
            if(in_array($key, $allowParams)){
                call_user_func(array($this->req,'set'.ucfirst($key)), $param);
            }
        }
        $result = $this->client->execute($this->req);
        return $result;
    }

    public function getConsumeList($groupName = "",$quantity = 100)
    {
        $this->consumeReq = new \TmcMessagesConsumeRequest();
        if($groupName){
            $this->consumeReq->setGroupName($groupName);
        }
        $this->consumeReq->setQuantity($quantity);
        $result = $this->client->execute($this->consumeReq);
        return $result;
    }

    public function confirmList($successMessageIds,$groupName = "",$failMessageIds="")
    {
        $this->confirmReq = new \TmcMessagesConfirmRequest();
        $this->confirmReq->setsMessageIds($successMessageIds);
        if($failMessageIds){
            $this->confirmReq->setfMessageIds($failMessageIds);
        }
        if($groupName){
            $this->confirmReq->setGroupName($groupName);
        }
        $result = $this->client->execute($this->confirmReq);
        return $result;
    }
}
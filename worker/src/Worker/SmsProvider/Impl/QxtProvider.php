<?php
namespace Worker\SmsProvider\Impl;

use Worker\SmsProvider\AbstractSmsProvider;

class QxtProvider extends AbstractSmsProvider
{
    protected $userAgent = 'EduSoho SMS Client 1.0';

    protected $connectTimeout = 15;

    protected $timeout = 15;

    protected $host = "http://211.151.85.133:8080";

    protected $username = "HZKZH";

    protected $passwordmd5 = "02b8ace1112b04ae";

    private $errorMsg = array("e-4" => "缺少短信内容","e-5" => "缺少目标号码","e-7" => "短信内容过长",
        "e-8" => "含有非法字符","e-9" => "目标号码格式错误","e-10" => "超过规定发送时间，禁止提交发送",
        "e-12" => "余额不足","e-15" => "发送内容前面需加签名","e-16" => "提交号码数量小于最小提交量限制",
        "e-20" => "未开通接口","e-22" => "短信内容签名不正确","e-99" => "连接失败",
        "e-100" => "系统内部错误", );

    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function send($dataArr)
    {

        $message = iconv('UTF-8', 'GBK', $dataArr['message']);
        $params = array("username" => $this->username,"passwordmd5" => $this->passwordmd5,"mobile" => $dataArr['mobile'],"message" => $message);
        $result = $this->sendRequest("GET", "/sendsms.asp", $params);
        if (intval($result)>0) {
            return array("success", intval($result));
        } else {
            return array($this->errorMsg["e{$result}"], intval($result));
        }
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

    private function sendRequest($method, $url, $params = array())
    {
        $curl = curl_init();
        $url = $this->host.$url;

        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        if (strtoupper($method) == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            $params = http_build_query($params);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else {
            if (!empty($params)) {
                $url = $url.(strpos($url, '?') ? '&' : '?').http_build_query($params);
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

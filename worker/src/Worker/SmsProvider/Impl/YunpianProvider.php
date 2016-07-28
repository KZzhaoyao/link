<?php
namespace Worker\SmsProvider\Impl;

use Worker\SmsProvider\AbstractSmsProvider;

class YunpianProvider extends AbstractSmsProvider
{
    private $sms_template = array(
        '1' => "【%s】您的验证码是%s",
        '2' => "【%s】您的验证码是%s。如非本人操作，请忽略本短信",
        '3' => "【%s】亲爱的%s，您的验证码是%s。如非本人操作，请忽略本短信",
        '4' => "【%s】亲爱的%s，您的验证码是%s。有效期为%s小时，请尽快验证",
        '5' => "【%s】感谢您注册%s，您的验证码是%s",
        '6' => "【%s】欢迎使用%s，您的手机验证码是%s。本条信息无需回复",
        '7' => "【%s】正在找回密码，您的验证码是%s",
        '8' => "【%s】激活码是#code#。如非本人操作，请致电%s",
        '9' => "【%s】%s(%s手机动态码，请完成验证)，如非本人操作，请忽略本短信",
        );
    protected $apikey = "cae33c1781eaf0b54581e37826eddd75";

    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function send($dataArr)
    {
        $uid = uniqid('yp', true);
        if (empty($dataArr['tpl_id'])) {
            return $this->sendSmsText($dataArr['mobile'], $dataArr['message'], $uid);
        } else {
            return $this->sendSmsTemplate($dataArr['mobile'], $dataArr['tpl_id'], $dataArr['name'],  $dataArr['code'], $uid);
        }
    }

    public function sendSmsText($mobile, $message, $uid)
    {
        $url = "http://yunpian.com/v1/sms/send.json";
        $encoded_text = urlencode($message);
        $post_string = "apikey={$this->apikey}&text={$encoded_text}&mobile={$mobile}&uid={$uid}";
        $result = $this->sock_post($url, $post_string);
        $result = json_decode($result, true);
        if (intval($result['code']) == 0) {
            return array("success", $uid);
        } else {
            return array($result['msg'], intval($result['code']));
        }
    }

    public function sendSmsTemplate($mobile, $tpl_id, $name, $code, $uid)
    {
        $url = "http://yunpian.com/v1/sms/tpl_send.json";
        $encoded_tpl_value = urlencode("#company#={$name}&#code#={$code}");  //tpl_value需整体转义
        $post_string = "apikey={$this->apikey}&tpl_id={$tpl_id}&tpl_value={$encoded_tpl_value}&mobile={$mobile}&uid={$uid}";

        $result = $this->sock_post($url, $post_string);
        $result = json_decode($result, true);
        if (intval($result['code']) == 0) {
            return array("success", $uid);
        } else {
            return array($result['msg'], intval($result['code']));
        }
    }

    public function getBalance()
    {
        $params = array("username" => $this->username,"passwordmd5" => $this->passwordmd5);

        return $this->sendRequest("GET", "/getBalance.asp", $params);
    }

    public function getExtensionNumFormat()
    {
        return "";
    }

    private function getMessage($params)
    {
        if (array_key_exists('tpl_id', $params)) {
            return $this->getTemplateMessage($params);
        }
        if (array_key_exists('message', $params)) {
            return $params['message'];
        }
        return '';
    }

    private function getTemplateMessage($params)
    {
        $template = $this->sms_template[$params['tpl_id']];
        if ($params['tpl_id'] == 1 || $params['tpl_id'] == 2 || $params['tpl_id'] == 7) {
            return sprintf($template, $params['name'], $params['code']);
        }
        if ($params['tpl_id'] == 3) {
            return sprintf($template, $params['name'], $params['user'], $params['code']);
        }
        if ($params['tpl_id'] == 4) {
            return sprintf($template, $params['name'], $params['user'], $params['code'], $params['hour']);
        }
        if ($params['tpl_id'] == 5 || $params['tpl_id'] == 6) {
            return sprintf($template, $params['name'], $params['app'], $params['code']);
        }
        if ($params['tpl_id'] == 8) {
            return sprintf($template, $params['name'], $params['code'], $params['tel']);
        }
        if ($params['tpl_id'] == 9) {
            return sprintf($template, $params['name'], $params['code'], $params['app']);
        }
    }

    /**
     * url 为服务的url地址
     * query 为请求串.
     */
    private function sock_post($url, $query)
    {
        $data = "";
        $info = parse_url($url);
        $fp = fsockopen($info["host"], 80, $errno, $errstr, 30);
        if (!$fp) {
            return $data;
        }
        $head = "POST ".$info['path']." HTTP/1.0\r\n";
        $head .= "Host: ".$info['host']."\r\n";
        $head .= "Referer: http://".$info['host'].$info['path']."\r\n";
        $head .= "Content-type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: ".strlen(trim($query))."\r\n";
        $head .= "\r\n";
        $head .= trim($query);
        $write = fputs($fp, $head);
        $header = "";
        while ($str = trim(fgets($fp, 4096))) {
            $header .= $str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp, 4096);
        }

        return $data;
    }
}

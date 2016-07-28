<?php
namespace Worker\SmsProvider;

abstract class AbstractSmsProvider
{
    public abstract function send($dataArr);

    public abstract function getBalance();

    public abstract function getExtensionNumFormat();

    public function getSendedSmsParams($params)
    {
        $num = count(explode(",", $params['mobile']));
        $templates = $params['sendTemplate']['templates'];
        $message = $this->_getHanldeMessage($templates);
        $fields = array(
            'userId' => $params['userId'],
            'mobile' => $params['mobile'],
            'message' => $message,
            'num' => $num,
            'category' => $params['category'],
            'requestedTime' => $params['requestedTime'],
        );
        if (array_key_exists('description', $params)) {
            $fields['description'] = $params['description'];
        }
        return $fields;
    }

    private function _getHanldeMessage($templates)
    {
        if(isset($templates['params']['verify'])){
            $verify = $this->_maskVerify($templates['params']['verify']);
            foreach($templates['patterns'] as $key => $value){
                if($value == '${verify}'){
                    $templates['replaces'][$key] = $verify;
                }
            }
        }
        return str_replace($templates['patterns'],$templates['replaces'],$templates['template']);
    }

    private function _maskVerify($verify)
    {
        $verify = $verify."";
        $length = strlen($verify);
        $half = intval($length/2);
        $maskNum = $length-$half;
        return substr($verify,0,$half).str_repeat('*',$maskNum);
    }
}

<?php

namespace EdusohoNet\Service\Util;

class ApiHttpClient
{
    public static function sendRequest($method, $url, $params, $headers = array())
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        if (strtoupper($method) == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } elseif (strtoupper($method) == 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        } else {
            if (!empty($params)) {
                $url = $url.(strpos($url, '?') ? '&' : '?').http_build_query($params);
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * 对提供的数据进行urlsafe的base64编码。
     *
     * @param string $data 待编码的数据，一般为字符串
     *
     * @return string 编码后的字符串
     * @link http://developer.qiniu.com/docs/v6/api/overview/appendix.html#urlsafe-base64
     */
    public static function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    /**
     * 对提供的urlsafe的base64编码的数据进行解码
     *
     * @param string $data 待解码的数据，一般为字符串
     *
     * @return string 解码后的字符串
     */
    public static function base64_urlSafeDecode($str)
    {
        $find = array('-', '_');
        $replace = array('+', '/');
        return base64_decode(str_replace($find, $replace, $str));
    }

    public static function sign($data, $accessKey, $secretKey)
    {
        $hmac = hash_hmac('sha1', $data, $secretKey, true);
        return $accessKey . ':' . self::base64_urlSafeEncode($hmac);
    }

    public static function signRequest($urlString, $body, $contentType = null, $accessKey, $secretKey)
    {
        $url = parse_url($urlString);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";
        if ($body != null &&
            ($contentType == 'application/x-www-form-urlencoded') ||  $contentType == 'application/json') {
            $data .= $body;
        }
        return self::sign($data, $accessKey, $secretKey);
    }
}

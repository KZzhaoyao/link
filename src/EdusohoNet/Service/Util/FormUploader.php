<?php

namespace EdusohoNet\Service\Util;

// use EdusohoNet\Service\Util\Client;

final class FormUploader
{

    public static function putFile(
        $upToken,
        $key,
        $filePath,
        $params,
        $mime,
        $checkCrc
    ) {

        $fields = array('token' => $upToken, 'file' => self::createFile($filePath, $mime));
        if ($key === null) {
            $fname = 'filename';
        } else {
            $fname = $key;
            $fields['key'] = $key;
        }
        if ($checkCrc) {
            $fields['crc32'] = self::crc32_file($filePath);
        }
        if ($params) {
            foreach ($params as $k => $v) {
                $fields[$k] = $v;
            }
        }
        $headers =array('Content-Type' => 'multipart/form-data');
        $response = Client::post('http://up.qiniu.com', $fields, $headers);
        if (!$response->ok()) {
            return array(null, new Error('http://up.qiniu.com', $response));
        }
        return array($response->json(), null);
    }

    /**
     * 计算文件的crc32检验码:
     *
     * @param $file string  待计算校验码的文件路径
     *
     * @return 文件内容的crc32校验码
     */
    private static function crc32_file($file)
    {
        $hash = hash_file('crc32b', $file);
        $array = unpack('N', pack('H*', $hash));
        return sprintf('%u', $array[1]);
    }

    private static function createFile($filename, $mime)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mime);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }

        return $value;
    }
}

<?php
namespace Worker;

use EdusohoNet\Common\ArrayToolkit;
use Footstones\Plumber\IWorker;
use EdusohoNet\Service\Common\ServiceKernel;

class EasyTrialWorker extends AbstractWorker
{
    public function process($data)
    {
        try {
            $dataArr = $data['body']['contents'];

            if (!ArrayToolkit::requireds($dataArr, array('domain', 'type', 'fromId', 'callbackUrl'))) {
                $this->logger->error("required fields error, jobId is {$data['id']}", $data);

                return IWorker::FINISH;
            }

            $this->logger->info("easy trial start. type:{$dataArr['type']} jobId is {$data['id']}", $data);
            $server = $this->_getTrialServer();
            call_user_func(array($this, '_'.$dataArr['type']), $dataArr, $server);
            
            return IWorker::FINISH;
            
        } catch (\Exception $e) {
            $this->logger->error("job #{$data['id']} is error. exception: {$e->getMessage()}", $data);

            return IWorker::FINISH;
        }
    }

    private function _open($dataArr, $server)
    {
        $remoteCmd = "php /data/www/BossKey/clone.php {$dataArr['domain']} {$dataArr['userName']} \"{$dataArr['password']}\" {$dataArr['email']}";
        $cmd = "ssh -p{$server['port']} {$server['user']}@{$server['host']} '{$remoteCmd}'";
        $this->logger->info("start do open trial, cmd: `{$cmd}`.");
        exec($cmd, $array, $statusCode);
        if ($statusCode != '0') {
            $this->logger->error("open trial fail: {$dataArr['domain']}", $array);
            return;
        }
        $params = array(
            'fromId' => $dataArr['fromId'],
            'status' => 'success',
            );
        $callbackResult = $this->_sendCallback($dataArr['callbackUrl'], $params);
        if ($callbackResult != 'true') {
            $this->logger->error("open trial callback error, url: {$dataArr['callbackUrl']}", $params);
        }
    }

    private function _close($dataArr, $server)
    {
        $remoteCmd = "php /data/www/BossKey/recover.php {$dataArr['domain']} ";
        $cmd = "ssh -p{$server['port']} {$server['user']}@{$server['host']} '{$remoteCmd}'";
        $this->logger->info("start do close trial, cmd: `{$cmd}`.");
        exec($cmd, $array, $statusCode);
        if ($statusCode != '0') {
            $this->logger->error("close trial fail: {$dataArr['domain']}", $array);
            return;
        }
        $params = array(
            'fromId' => $dataArr['fromId'],
            'status' => 'close',
            );
        $callbackResult = $this->_sendCallback($dataArr['callbackUrl'], $params);
        if ($callbackResult != 'true') {
            $this->logger->error("close trial callback error, url: {$dataArr['callbackUrl']}", $params);
        }
    }

    private function _lock($dataArr, $server)
    {
        $remoteCmd = "php /data/www/BossKey/lock.php {$dataArr['domain']} ";
        $cmd = "ssh -p{$server['port']} {$server['user']}@{$server['host']} '{$remoteCmd}'";
        $this->logger->info("start do lock trial, cmd: `{$cmd}`.");
        exec($cmd, $array, $statusCode);
        if ($statusCode != '0') {
            $this->logger->error("lock trial fail: {$dataArr['domain']}", $array);
            return;
        }
        $params = array(
            'fromId' => $dataArr['fromId'],
            'status' => 'lock',
            );
        $callbackResult = $this->_sendCallback($dataArr['callbackUrl'], $params);
        if ($callbackResult != 'true') {
            $this->logger->error("lock trial callback error, url: {$dataArr['callbackUrl']}", $params);
        }
        
    }

    private function _unlock($dataArr, $server)
    {
        $remoteCmd = "php /data/www/BossKey/unlock.php {$dataArr['domain']} ";
        $cmd = "ssh -p{$server['port']} {$server['user']}@{$server['host']} '{$remoteCmd}'";
        $this->logger->info("start do unlock trial, cmd: `{$cmd}`.");
        exec($cmd, $array, $statusCode);
        if ($statusCode != '0') {
            $this->logger->error("unlock trial fail: {$dataArr['domain']}", $array);
            return;
        }
        $params = array(
            'fromId' => $dataArr['fromId'],
            'status' => 'success',
            );
        $callbackResult = $this->_sendCallback($dataArr['callbackUrl'], $params);
        if ($callbackResult != 'true') {
            $this->logger->error("unlock trial callback error, url: {$dataArr['callbackUrl']}", $params);
        }
    }

    private function _getTrialServer()
    {
        return ServiceKernel::instance()->getParameter('trial')['trialServers']['server1'];
    }

    private function _sendCallback($callbackUrl, $params)
    {
        return $this->_sendRequest('POST', $callbackUrl, $params);
    }

    private function _sendRequest($method, $url, $params = array())
    {
        $curl = curl_init();
        // curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
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

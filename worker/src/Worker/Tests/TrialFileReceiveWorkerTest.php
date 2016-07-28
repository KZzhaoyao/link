<?php
namespace Worker\Tests;

use EdusohoNet\Common\MessageQueueToolkit;
use EdusohoNet\Common\BeanstalkClient;
use EdusohoNet\Service\Common\BaseServiceTestCase;

class TrialFileReceiveWorkerTest extends BaseServiceTestCase
{
    public function testReceive()
    {
        // $params = array(
        //     'userId' => 13050,
        //     'fileId' => 2,
        //     'originalFileName' => 'courselesson-32/20151113123240-2tv3lpyq69k44cco/1bac1335667b1964_sd',
        //     'quality' => 'sd',
        //     );
        // $jobData = MessageQueueToolkit::createJob('trialFileReceiveTube', $params);
        // BeanstalkClient::pushJob("trialFileReceiveTube", json_encode($jobData));
    }
}

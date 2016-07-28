### 1.安装beanstalkd.

    sudo apt-get install beanstalkd

### 2.安装swoole 

    sudo apt-get install php5 php5-dev gcc autoconf libpcre3-dev
    
    drools@wenqin-PC:  git clone https://github.com/swoole/swoole-src.git	
    drools@wenqin-PC:  cd swoole-src
    drools@wenqin-PC:  phpize
    drools@wenqin-PC:  ./configure
    drools@wenqin-PC:  sudo make
    drools@wenqin-PC:  sudo make install
    drools@wenqin-PC:  sudo vi /etc/php5/cli/php.ini

   =========================================

    [curl] 
    ; A default value for the CURLOPT_CAINFO option. This is required to be an 
    ; absolute path. 
    ;curl.cainfo = 
    extension=swoole.so 
    ; Local Variables: 
    ; tab-width: 4 
    ; End: 

    =======================
    php -m

### 3. 配置参数

    'async' => array(
        'host' => '127.0.0.1',
        'port' => '11300',
        'liveTimeout' => 10,
        'apiSecret' => 'hello123456',
        'processManager' => array(
            'SmsProcessManager',
            'LiveProcessManager',
            ),
        'processes' => array(
            'SmsProcess',
            'LiveProcess',
            ),
        'processTubes' => array(
            'smsSendTube',
            'liveOnlineNumTube',
            ),
        ),

|参数名|描述|
|:---------|:----------|
|host|beanstalkd监听host|
|port|beanstalkd监听端口|
|liveTimeout|直播处理间隔时间|
|apiSecret|调用api的secretkey|
|processManager|处理的manager|
|processes|处理|
|processTubes|beanstalkd的管道|


### 4.启动worker
    
    vendor/bin/plumber start worker/app/config.php


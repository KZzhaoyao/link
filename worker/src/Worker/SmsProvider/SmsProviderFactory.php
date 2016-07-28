<?php

namespace Worker\SmsProvider;

class SmsProviderFactory
{
    protected $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    public function create($provider = 'Qxt')
    {
        $class = __NAMESPACE__ . "\\Impl\\{$provider}Provider";
        return new $class($this->logger);
    }

    public function getLogger()
    {
        return $this->logger;
    }
}

<?php

namespace SmartboxSkeletonBundle\Services;


use Smartbox\Integration\FrameworkBundle\Core\Exchange;

/**
 * Class DummyService.
 */
class DummyService
{
    protected $logger;
    protected $kernel;

    public function __construct($logger, $kernel) {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    public function logContent(Exchange $exchange) {
        $body = $exchange->getIn()->getBody();

    }

    public function doNothing(Exchange $exchange){
        //Does exactly what it says on the tin
    }
}

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

    /**
     * DummyService constructor.
     *
     * @param $logger
     * @param $kernel
     */
    public function __construct($logger, $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    /**
     * @param Exchange $exchange
     */
    public function logContent(Exchange $exchange)
    {
        $body = $exchange->getIn()->getBody();
        $this->logger->info($body);
    }

    /**
     * @param Exchange $exchange
     */
    public function doNothing(Exchange $exchange)
    {
        //Does exactly what it says on the tin
    }
}

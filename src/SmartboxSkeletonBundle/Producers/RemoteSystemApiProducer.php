<?php

declare(strict_types=1);

namespace SmartboxSkeletonBundle\Producers;

use Smartbox\Integration\FrameworkBundle\Components\WebService\Rest\RestConfigurableProducer;
use Smartbox\Integration\FrameworkBundle\Tools\SmokeTests\CanCheckConnectivityInterface;

/**
 * Class RemoteSystemApiProducer.
 */
class RemoteSystemApiProducer extends RestConfigurableProducer implements CanCheckConnectivityInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkConnectivityForSmokeTest(array $config = [])
    {
        //not implemented for now
    }

    /**
     * {@inheritdoc}
     */
    public static function getConnectivitySmokeTestLabels()
    {
        return 'important';
    }
}

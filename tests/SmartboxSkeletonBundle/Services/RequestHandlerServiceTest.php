<?php

namespace SmartboxSkeletonBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use SmartboxSkeletonBundle\Entity\PingMessage;
use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
/**
 * Class RequestHandlerServiceTest
 * @package SmartboxSkeletonBundle\Tests\Services
 */
class RequestHandlerServiceTest extends KernelTestCase
{
    public function testRequestHandler()
    {
        $requestHandler = $this->getContainer()->get('smartbox_skeleton_request_handler');
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0';
        $request = new Request();
        $request->headers = new HeaderBag(['User-Agent' => $userAgent]);

        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage("Ping");

        $context = new Context([
            Context::FLOWS_VERSION => "0",
            Context::TRANSACTION_ID => uniqid('', true),
            Context::ORIGINAL_FROM => 'api',
        ]);

        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            'ping',
            $pingMessage,
            [],
            $context,
            false
        );
        $response = $responseMessage->getBody();
        $responseContext = $responseMessage->getContext();
        $transactionId = $responseContext->get('transaction_id');
        $this->assertNotNull($transactionId);
        $this->assertEquals('Pong',$response['message']);

    }

    public function getContainer()
    {
        self::bootKernel();
        return self::$kernel->getContainer();
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        static::bootKernel($options);

        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }
}
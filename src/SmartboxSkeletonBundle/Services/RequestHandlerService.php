<?php

namespace SmartboxSkeletonBundle\Services;

use Smartbox\Integration\FrameworkBundle\Core\Endpoints\EndpointFactory;
use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
use Smartbox\Integration\FrameworkBundle\Core\Messages\Message;

/**
 * Class RequestHandlerService.
 */
class RequestHandlerService
{
    protected $kernel;

    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param $serviceName
     * @param $apiVersion
     * @param $methodName
     * @param $messageBody
     * @param $messageHeaders
     * @param $context
     * @param bool $async
     *
     * @return mixed
     */
    public function handleCall(
        $serviceName,
        $apiVersion,
        $methodName,
        $messageBody,
        $messageHeaders,
        $context,
        $async = false
    ) {
        $apiPrefix = 'api';
        $fromUri = $apiPrefix.'://entry/'.$serviceName.'/'.$apiVersion.'/'.$methodName;
        $helper = $this->getContainer()->get('smartesb.helper');
        $messageFactory = $helper->getMessageFactory();
        $contextExtra = [];
        $contextExtra['from'] = $fromUri;
        $priority = 'normal';
        $contextExtra['api_mode'] = 'real';
        $contextExtra['priority'] = $priority;
        $context = new Context(array_merge($context->toArray(), $contextExtra));
        $messageHeaders[Message::HEADER_FROM] = $fromUri;
        $messageHeaders['api_mode'] = 'real';
        $messageHeaders['async'] = $async ? 'true' : 'false';
        $message = $messageFactory->createMessage($messageBody, $messageHeaders, $context);
        $endpoint = $this->getContainer()->get('smartesb.endpoint_factory')->createEndpoint($message->getHeader(Message::HEADER_FROM), EndpointFactory::MODE_CONSUME);
        $resultMessage = $endpoint->handle($message);

        return $resultMessage;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->kernel->getContainer();
    }
}

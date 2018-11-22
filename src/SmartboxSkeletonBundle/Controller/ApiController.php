<?php

declare(strict_types=1);

namespace SmartboxSkeletonBundle\Controller;

use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
use SmartboxSkeletonBundle\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @param $methodName
     *
     * @return Response
     */
    public function apiAction(Request $request, $methodName)
    {
        if ('' === $methodName) {
            return $this->render('SmartboxSkeletonBundle:Default:index.html.twig');
        }

        if ('POST' === $request->getMethod()) {//assumed always async for demo
            $serializer = $this->get('jms_serializer');
            $content = $request->getContent();
            $data = null;
            switch ($methodName) {
                case 'asyncping':
                case 'ping':
                    $data = $serializer->deserialize($content, 'SmartboxSkeletonBundle\Entity\PingMessage', 'json');
                    break;
                default:
                    return new Response('{"status":"failed"}', Response::HTTP_METHOD_NOT_ALLOWED, ['Content-Type' => 'application/json']);
            }

            $responseMessage = $this->send($methodName, $data, true);
            $responseContext = $responseMessage->getContext();
            $transactionId = $responseContext->get('transaction_id');
            $code = $responseMessage->getBody()->getCode();
            $json = $serializer->serialize($responseMessage->getBody(), 'json');
            $response = new Response($json, $code, ['Content-Type' => 'application/json']);
            $response->headers->set('transactionId', $transactionId);

            return $response;
        } elseif ('GET' === $request->getMethod()) {//assumed always sync for demo
            $data = new Result(); //No data
            $responseMessage = $this->send($methodName, $data, false);
            $responseContext = $responseMessage->getContext();
            $transactionId = $responseContext->get('transaction_id');
            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($responseMessage->getBody(), 'json');
            $response = new Response($json, 200, ['Content-Type' => 'application/json']);
            $response->headers->set('transactionId', $transactionId);

            return $response;
        }

        return new Response('{"status":"failed"}', Response::HTTP_METHOD_NOT_ALLOWED, ['Content-Type' => 'application/json']);
    }

    /**
     * This function just accepts the request, logs it and always returns a 200.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function pingCallbackAction(Request $request)
    {
        $logger = $this->get('logger');
        $content = $request->getContent();
        $logContent = \json_encode($content);
        $logger->info($logContent);

        return new Response('{"status":"ok"}', Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @param $methodName
     * @param $data
     * @param $async
     *
     * @return mixed
     */
    protected function send($methodName, $data, $async)
    {
        $requestHandler = $this->get('smartesb_skeleton_request_handler');
        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => \uniqid('', true),
            Context::ORIGINAL_FROM => 'api',
        ]);

        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            $methodName,
            $data,
            [],
            $context,
            $async
        );

        return $responseMessage;
    }
}

<?php

namespace SmartboxSkeletonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
use SmartboxSkeletonBundle\Entity\Result;

class ApiController extends Controller
{
    public function apiAction(Request $request, $methodName)
    {
        if ('' == $methodName) {
            return $this->render('SmartboxSkeletonBundle:Default:index.html.twig');
        }

        if ('POST' == $request->getMethod()) {//assumed always async for demo
            $serializer = $this->get('jms_serializer');
            $content = $request->getContent();
            $data = null;
            switch ($methodName) {
                case 'asyncping':
                case 'ping':
                    $data = $serializer->deserialize($content, 'SmartboxSkeletonBundle\Entity\PingMessage', 'json');
                    break;
                default:
                    return new Response('{"status":"failed"}', Response::HTTP_METHOD_NOT_ALLOWED, array('Content-Type' => 'application/json'));
            }

            $responseMessage = $this->send($methodName, $data, true);
            $responseContext = $responseMessage->getContext();
            $transactionId = $responseContext->get('transaction_id');
            $code = $responseMessage->getBody()->getCode();
            $json = $serializer->serialize($responseMessage->getBody(), 'json');
            $response = new Response($json, $code, array('Content-Type' => 'application/json'));
            $response->headers->set('transactionId', $transactionId);

            return $response;
        } elseif ('GET' == $request->getMethod()) {//assumed always sync for demo
            $data = new Result(); //No data
            $responseMessage = $this->send($methodName, $data, false);
            $responseContext = $responseMessage->getContext();
            $transactionId = $responseContext->get('transaction_id');
            $serializer = $this->get('jms_serializer');
            $json = $serializer->serialize($responseMessage->getBody(), 'json');
            $response = new Response($json, 200, array('Content-Type' => 'application/json'));
            $response->headers->set('transactionId', $transactionId);

            return $response;
        }

        return new Response('{"status":"failed"}', Response::HTTP_METHOD_NOT_ALLOWED, array('Content-Type' => 'application/json'));
    }

    protected function send($methodName, $data, $async)
    {
        $requestHandler = $this->get('smartbox_skeleton_request_handler');
        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => uniqid('', true),
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

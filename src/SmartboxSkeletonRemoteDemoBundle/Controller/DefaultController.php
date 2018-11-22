<?php

declare(strict_types=1);

namespace SmartboxSkeletonRemoteDemoBundle\Controller;

use SmartboxSkeletonBundle\Entity\PingMessage;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
=======
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
>>>>>>> 6b5f7de0c3883d3d0182af2d35efce3ce3305129
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SmartboxSkeletonRemoteDemoBundle:Default:index.html.twig');
    }

    public function pongAction()
    {
        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage('Pong');
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($pingMessage, 'json');

        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * This function just accepts the request, logs it and always returns a 200.
     *
     * @param Request $request
     */
    public function logContentAction(Request $request)
    {
        $logger = $this->get('logger');
        $content = $request->getContent();
        $logContent = \json_encode($content);
        $logger->info($logContent);

        return new JsonResponse(['status' => 'ok']);
    }
}

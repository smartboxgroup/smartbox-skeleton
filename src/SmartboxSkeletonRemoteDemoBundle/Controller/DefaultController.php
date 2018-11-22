<?php

declare(strict_types=1);

namespace SmartboxSkeletonRemoteDemoBundle\Controller;

use SmartboxSkeletonBundle\Entity\PingMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}

<?php

namespace SmartboxSkeletonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SmartboxSkeletonBundle:Default:index.html.twig');
    }
}

<?php

namespace Snappminds\Utils\Bundle\BreadcrumbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('SnappmindsUtilsBreadcrumbBundle:Default:index.html.twig', array('name' => $name));
    }
}

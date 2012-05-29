<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Controller;
          
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function headersAction()
    {        
        return $this->render('SnappmindsWidgetsBundle:Default:headers.html.twig',
                array()
		);
    }    
}

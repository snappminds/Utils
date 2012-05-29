<?php

namespace Snappminds\Utils\Bundle\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function headersAction()
    {
        return $this->render('SnappmindsUtilsFormBundle:Default:headers.html.twig',
                array()
		);

    }
}

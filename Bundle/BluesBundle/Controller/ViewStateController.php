<?php

namespace Snappminds\Utils\Bundle\BluesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class ViewStateController extends BaseController
{
    public function headersAction()
    {        
        return $this->render('SnappmindsUtilsBluesBundle:ViewState:headers.html.twig',
                array()
		);
    }    
}

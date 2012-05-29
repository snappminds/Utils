<?php

namespace Snappminds\Utils\Bridge\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArrayExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            'array_pair' => new \Twig_Function_Method($this, 'getPair', array('is_safe' => array('html')))
        );
    }
    
    public function getPair($key, $value)
    {
        return array($key => $value);
    }       
    

    public function getName()
    {
        return 'array';
    }

}

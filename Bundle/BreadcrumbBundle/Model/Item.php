<?php

namespace Snappminds\Utils\Bundle\BreadcrumbBundle\Model;

class Item
{
    private $title;
    private $routeData;
    
    public function __construct($title, $routeData)
    {
        $this->title = $title;
        $this->routeData = $routeData;
    }    

    public function getTitle()
    {
        return $this->title;
    }

    public function getRouteData()
    {
        return $this->routeData;
    }    
}
<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

class DateTimeColumn extends Column
{
    public function buildView(View $view)
    {
        $view->set('label', $this->getLabel());
        $view->set('name', $this->getName());
        $view->set('type', 'datetimecolumn');

        return $view;
    }    
}

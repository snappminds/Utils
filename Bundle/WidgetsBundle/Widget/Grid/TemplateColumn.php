<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

/**
 * 
 */
class TemplateColumn extends Column
{

    private $blockName;
    
    public function setBlockName($value)
    {
        $this->blockName = $value;
    }
    
    public function getBlockName()
    {
        return $this->blockName;
    }
    
    public function buildView(View $view)
    {
        parent::buildView($view);
        $view->set('type', 'templatecolumn');
        
        $view->set('blockName', 'templatecolumn_' . $this->getBlockName() . '_template');
        
        return $view;
    }
    

}

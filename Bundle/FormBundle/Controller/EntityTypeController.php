<?php

namespace Snappminds\Utils\Bundle\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;

class EntityTypeController extends Controller
{
    function entityTypeDataAction(Request $request, $entitytype, $qbParamName, $dataField, $filterType = 'BEGINS_WITH')
    {
        $ajaxEntityType = $this->container->get('form.factory')->createBuilder($entitytype, null, array('required' => false));
        
        switch ($filterType)
        {
            case "BEGINS_WITH":
                    $string = $request->query->get($dataField) . '%';
                    break;
            case "ENDS_WITH":
                    $string = '%' . $request->query->get($dataField);
                    break;
            case "CONTAINS":
                    $string = '%' . $request->query->get($dataField) . '%';    
                    break;                
        }
        
        return $this->forward(
                        'SnappmindsUtilsFormBundle:AjaxType:ajaxResponseFromString', array(
                        'ajaxEntityType' => $ajaxEntityType,
                        'qbParamName' => $qbParamName,
                        'string' => $string
                        )
        );
    }
}

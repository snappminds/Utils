<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

class ActionLinkColumn extends ActionColumn
{

    /**
     * Agrega un action a la lista de acciones posibles
     *
     * @param string Identificador de la acción
     * @param array Arreglo asociativo con información de la acción.
     */
    public function setAction($name, array $options)
    {
        if (!isset($options['url']) && !isset($options['route']))
            throw new \LogicException('Debe definir la opción \'url\' o la opción \'route\'');

        if (isset($options['url']) && isset($options['route']))
            throw new \LogicException('No puede definir las opciones \'url\' y \'route\' al mismo tiempo.');

        return parent::setAction($name, $options);
    }

    public function buildView(View $view)
    {
        parent::buildView($view);
        $view->set('type', 'actionlinkcolumn');

        $actionsView = $view->get('actions');

        foreach ($this->getActions() as $name => $action) {
            $actionView = $actionsView[$name];
            
            if (isset($action['url']))
                $actionView['url'] = $action['url'];
            else
                $actionView['route'] = $action['route'];

            if (isset($action['params']))
                $actionView['params'] = $action['params'];
            else
                $actionView['params'] = array();
            
            /**
             * Nombres de los parámetros HTTP para cada data field.
             * 
             * Se inicializan con el mismo nombre que el data field.
             */
            $dataFieldParamNames = array();
            foreach ($this->getDataFields() as $dataFieldName) {
                $dataFieldParamNames[$dataFieldName] = $dataFieldName;
            }
            
            if (isset($action['dataFieldParamNames'])) {
                
                $dataFieldParamNames = array();
                foreach ($action['dataFieldParamNames'] as $fieldName => $paramName) {
                    $dataFieldParamNames[$fieldName] = $paramName;
                }
            }
            
            $actionView['dataFieldParamNames'] = $dataFieldParamNames; 
              
            if (isset($action['blockName']))
                $actionView['blockName'] = 'actionlinkcolumn_' . $action['blockName'] . '_template';
            else
                $actionView['blockName'] = 'actionlinkcolumn_action_template';
            
            $actionsView[$name] = $actionView;
        }

        $view->set('actions', $actionsView);

        return $view;
    }

}

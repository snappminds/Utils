<?php

namespace Snappminds\Utils\Bundle\ABMBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid\Grid;
use Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid\ActionLinkColumn;

/**
 * Grilla para módulos ABM de entidades.
 * 
 * Esta grilla muestra por defecto una columna de "actions" con las acciones
 * de Insert y Update.
 * Por defecto se asume que la clave primaria de la entidad es el campo "id".
 * Este campo se utiliza para llevar a cabo los Insert y Update.
 * 
 * Si se va a modificar el "theme" se recomienda 
 * heredar de SnappmindsUtilsABMBundle:Grid:blocks.html.twig
 * 
 * 
 *
 * @author gcaseres
 */
class ABMGrid extends Grid {
 
    public function setCriteria(array $value) {
        $this->getDataSource()->setCriteria($value);
    }

    public function getCriteria() {
        return $this->getDataSource()->getCriteria();
    }

    protected function initializeFromStateData($stateData) {
        parent::initializeFromStateData($stateData);

        if ($stateData->has('criteria')) {
            $this->setCriteria($stateData->get('criteria'));
        }
    }

    public function hasActionColumn() {
        return $this->hasColumn('actions');
    }
    
    public function getActionColumn() {
        return $this->getColumn('actions');
    }
    
    public function addAction($name, array $options){
        $this->getActionColumn()->setAction($name, $options);
        return $this;
    }
    
    public function removeAction($name){
        $this->getActionColumn()->removeAction($name);
        return $this;
    }    

    protected function getColumnOrder() {
        $columnOrder = parent::getColumnOrder();

        if( in_array('actions', $columnOrder) ){
            
            unset($columnOrder[array_search('actions', $columnOrder)]);
            array_push($columnOrder, 'actions');            
        }

        return $columnOrder;
    }

    public function setUpdateRoute($value, $params = array()) {
        $action = $this->getActionColumn()->getAction('update');

        $action['route'] = $value;
        $action['params'] = $params;

        $this->getActionColumn()->setAction('update', $action);
    }

    public function getUpdateRoute() {
        $action = $this->getActionColumn()->getAction('update');

        return array(
            'route' => $action['route'],
            'params' => $action['params']
        );
    }

    public function setDeleteRoute($value, $params = array()) {
        $action = $this->getActionColumn()->getAction('delete');

        $action['route'] = $value;
        $action['params'] = $params;

        $this->getActionColumn()->setAction('delete', $action);
    }

    public function getDeleteRoute() {
        $action = $this->getActionColumn()->getAction('delete');

        return array(
            'route' => $action['route'],
            'params' => $action['params']
        );
    }

    protected function initialize() {
        $actionColumn = new ActionLinkColumn('actions', 'Acciones');
        
        $actionColumn->setAction( 'update', array( 'label' => 'Modificar', 'route' => '', 'params' => array() ) )
                     ->setAction( 'delete', array( 'label' => 'Eliminar', 'route' => '', 'params' => array(), 'blockName' => 'SnappmindsUtilsABMBundleABMDeleteAction' ) )
                     ->setDataFields(array('id'));
        
        $this->setColumn($actionColumn);
    }

    /**
     * Crea una vista de la grilla.
     *
     * @return GridView La vista
     */
    public function createView() {
        $view = parent::createView();

        $view->set('criteria', $this->getCriteria());
        $view->set('theme', 'SnappmindsUtilsABMBundle:Grid:blocks.html.twig');

        return $view;
    }

}

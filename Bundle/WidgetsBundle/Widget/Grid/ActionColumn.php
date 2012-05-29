<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

class ActionColumn extends TemplateColumn
{

    private $actions;
    private $dataFields = array();

    public function __construct($name, $label)
    {
        parent::__construct($name, $label);

        $this->setActions(new \ArrayObject());
        $this->setBlockName('actioncolumn');
    }

    protected function setActions($value)
    {
        $this->actions = $value;
    }

    protected function getActions()
    {
        return $this->actions;
    }

    /**
     * Establece los campos que serán utilizados para ejecutar los actions.
     *
     * @param array Arreglo de nombres de campos.
     */
    public function setDataFields(array $value)
    {
        $this->dataFields = $value;
    }

    /**
     * Devuelve los campos que serán utilizados para ejecutar los actions.
     *
     * @return array Arreglo de nombres de campos.
     */
    public function getDataFields()
    {
        return $this->dataFields;
    }

    /**
     * Agrega un action a la lista de acciones posibles
     *
     * @param string Identificador de la acción
     * @param array Arreglo asociativo con información de la acción.
     */
    public function setAction($name, array $options)
    {
        $this->getActions()->offsetSet($name, $options);
        return $this;
    }
    
    public function removeAction($name)
    {
        $this->getActions()->offsetUnset($name);
    }
    
    public function getAction($name)
    {
        return $this->getActions()->offsetGet($name);
    }
    
    public function hasAction($name)
    {
        return $this->getActions()->offsetExists($name);
    }

    public function buildView(View $view)
    {
        parent::buildView($view);
        $view->set('type', 'actioncolumn');

        $actionsView = array();

        foreach ($this->getActions() as $name => $action) {
            $actionView['name'] = $name;
            $actionView['label'] = isset($action['label']) ? $action['label'] : ucfirst($name);

            if (isset($action['blockName']))
                $actionView['blockName'] = 'actioncolumn_' . $action['blockName'] . '_template';
            else
                $actionView['blockName'] = 'actioncolumn_action_template';

            $actionsView[$name] = $actionView;
        }

        $view->set('actions', $actionsView);

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function processValue($value)
    {
        $result = array();
        foreach ($this->getDataFields() as $dataField) {
            $result[$dataField] = $value[$dataField];
        }

        return $result;
    }

}

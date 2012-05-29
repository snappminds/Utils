<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

class Column
{

    private $name;
    private $label;

    public function __construct($name, $label)
    {
        $this->setName($name);
        $this->setLabel($label);
    }

    protected function setName($value)
    {
        $this->name = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setLabel($value)
    {
        $this->label = $value;
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Procesa una fila o conjunto de valores para generar el valor de una celda.
     *
     * @param $value Fila o conjunto de valores a procesar.
     */
    public function processValue($row)
    {
        return $row[$this->getName()];
    }

    public function buildView(View $view)
    {
        $view->set('label', $this->getLabel());
        $view->set('name', $this->getName());
        $view->set('type', 'column');

        return $view;
    }

}

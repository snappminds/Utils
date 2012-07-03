<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;
use Snappminds\Utils\Bundle\WidgetsBundle\DataSource\IDataSource;
use Snappminds\Utils\Bundle\WidgetsBundle\DataSource\ArrayDataSource;
use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

/**
 * Grilla genérica para mostrar información de manera tabular y con funciones
 * de paginación.
 * 
 * 
 * 
 * @author gcaseres
 */
abstract class Grid
{

    private $id;
    private $dataSource;
    private $controller;
    private $currentPage;
    private $rowsPerPage;
    private $actionRoute;
    private $defaulRouteParams;
    private $columns;
    private $columnOrder = array();

    public function __construct(IDataSource $dataSource, Controller $controller, $id = 'grid', $actionRoute = null)
    {
        $this->setDataSource($dataSource);
        $this->controller = $controller;
        $this->setId($id);
        $this->setActionRoute($actionRoute);
        $this->setColumns(new \ArrayObject());

        /* Inicializar el estado de la grilla según los parámetros GET recibidos */
        $request = $this->controller->get('request');

        if ($request->query->has($this->getId())) {
            $stateData = new ParameterBag($request->query->get($this->getId()));
        } else {
            $stateData = new ParameterBag(array());
        }
        
        $this->initializeFromStateData($stateData);

        $this->initialize();
    }

    protected function initializeFromStateData($stateData)
    {
        if ($stateData->has('page')) {
            $this->setCurrentPage($stateData->get('page'));
        } else {
            $this->setCurrentPage(1);
        }        
    }
    
    protected abstract function initialize();

    protected function setColumnOrder(array $value = array())
    {
        $this->columnOrder = $value;
    }
    
    protected function getColumnOrder()
    {
        $columnOrder = $this->columnOrder;
        if (count($columnOrder) == 0)
            $columnOrder = array_keys($this->getColumns()->getArrayCopy());
        
        return $columnOrder;
    }
    
    public function setColumn(Column $column)
    {        
        $this->getColumns()->offsetSet($column->getName(), $column);
        return $this;
    }

    public function getColumn($name)
    {
        return $this->getColumns()->offsetGet($name);
    }

    protected function setColumns(\ArrayObject $value)
    {
        $this->columns = $value;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Devuelve la información de la grilla en forma de matriz según las 
     * columnas definidas.
     * 
     * Este método invoca al DataSource asociado a la grilla.
     * 
     * @return array Información a mostrar de la grilla.
     */
    public function getData()
    {
        $rows = $this->getDataSource()->getData($this->getCurrentPage(), $this->getRowsPerPage());

        foreach ($rows as $key => $row) {
            foreach ($this->getColumns() as $column) {
                $id = $column->getName();

                $rows[$key][$id] = $column->processValue($row);
            }
        }

        return $rows;
    }

    public function setCurrentPage($value)
    {
        $this->currentPage = $value;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function getPageCount()
    {
        return ceil($this->getDataSource()->getCount() / $this->getRowsPerPage());
    }

    public function setRowsPerPage($value)
    {
        $this->rowsPerPage = $value;
    }

    public function getRowsPerPage()
    {
        return $this->rowsPerPage;
    }

    /**
     * Establece el identificador único de la grilla en la vista.
     * 
     * @param string $value Cadena de identificación.
     */
    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * Devuelve el identificador único de la grilla en la vista.
     * 
     * @return string Cadena de identificación. 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Establece la ruta a acceder cuando se utiliza alguna funcionalidad
     * de la grilla. (paginación, refresh, etc.)
     * 
     * 
     * @param string $value Nombre de la ruta
     * @param array $params Arreglo asociativo con parámetros HTTP.
     */
    public function setActionRoute($value, $params = array())
    {
        if (!$value)
            $value = $this->controller->get('request')->get('_route');

        $this->actionRoute = array('route' => $value, 'params' => $params);
    }
    
    /**
     * Establece la ruta a acceder cuando se utiliza alguna funcionalidad
     * de la grilla. (paginación, refresh, etc.)
     * 
     * Devuelve un arreglo asociativo con dos elementos:
     * route => Nombre de la ruta.
     * params => Arreglo asociativo con parámetros HTTP.
     * 
     * @return array Arreglo asociativo con infomación de la ruta.
     */
    public function getActionRoute()
    {
        return $this->actionRoute;
    }

    public function setDefaultRouteParams( $params )
    {
        return $this->defaulRouteParams = $params;
    }     

    public function getDefaulRouteParams()
    {
        return $this->defaulRouteParams;
    }
        
    /**
     * Crea una vista de la grilla.
     *
     * @return GridView La vista
     */
    public function createView()
    {
        $rows = $this->getData();
        
        if( $this->getDataSource()->getCount() == 0 ){
            $firstRowNumber = 0;
            $lastRowNumber = 0;    
            $currentPage = 0;
        }else{
            $firstRowNumber = ($this->getCurrentPage() - 1) * $this->getRowsPerPage() + 1;    
            $lastRowNumber = $firstRowNumber + count($rows) - 1; 
            $currentPage = $this->getCurrentPage();
        }
        
        $view = new View();
        
        $view->set('id', $this->getId());
        $view->set('theme', 'SnappmindsWidgetsBundle:Grid:ajax-blocks.html.twig');
        $view->set('ajaxBrowseTemplate', 'SnappmindsWidgetsBundle:Grid:ajax.html.twig');
        $view->set('rowCount', $this->getDataSource()->getCount());
        $view->set('currentPage', $currentPage);
        $view->set('pageCount', $this->getPageCount());
        $view->set('actionRoute', $this->getActionRoute());
        $view->set('defaultRouteParams', $this->getDefaulRouteParams());
        $view->set('firstRowNumber', $firstRowNumber);
        $view->set('lastRowNumber', $lastRowNumber);
        $view->set('rows', $rows);
	$view->set('className', '');


        $columns = array();
        

        foreach ($this->getColumnOrder() as $columnName) {            
            $column = $this->getColumn($columnName);
            
            $columns[$columnName] = $column->buildView(new View());
        }

        $view->set('columns', $columns);

        return $view;
    }
    
    protected function setDataSource(IDataSource $value)
    {
        $this->dataSource = $value;
    }
    
    protected function getDataSource()
    {
        return $this->dataSource;
    }     
}

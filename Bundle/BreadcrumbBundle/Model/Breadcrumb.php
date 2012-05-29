<?php

namespace Snappminds\Utils\Bundle\BreadcrumbBundle\Model;

use Snappminds\Utils\Bundle\BluesBundle\View\View;
use Snappminds\Utils\Bundle\BluesBundle\Model\ViewState;

use \Countable;
use \Iterator;

class Breadcrumb implements Countable, Iterator 
{
    protected $iterator = 0;
    protected $viewState;
    protected $trail = array();
    
    const HIERARCHY_SEPARATOR = ' > ';
    const STORAGE_PARAM_NAME = '_bctrail';

    public function load(ViewState $viewState){
        $this->viewState = $viewState;
        $this->trail = $viewState->get( self::STORAGE_PARAM_NAME );
    }

    public function getViewState()
    {
        return $this->viewState;
    }
    
    public function getTrail()
    {      
        if (! $this->getViewState()->has( self::STORAGE_PARAM_NAME ) ){
            $this->reset();
        }
        
        return $this->getViewState()->get( self::STORAGE_PARAM_NAME );
    }   

    /**
     * add an element to the breadcrumb
     */
    public function add(Item $item )
    {        
        $trail = $this->getTrail();
        
        /**
         * Checks if the new url is in the array
         */
        $foundKey = false;
        foreach( $trail as $key => $element ){
            
            $elementRouteData = $element->getRouteData();
            $itemRouteData = $item->getRouteData();
            
            if( $elementRouteData['route'] == $itemRouteData['route'] ){
                $foundKey = $key;                
            }
        }
        
        // Atención que $foundKey podría ser 0 si el objeto esta en la primer posición. Problema de 0 = false
        if( $foundKey !== false ){
            $trail = array_slice( $trail, 0, $foundKey+1 );
        }else{
            $trail[] = $item;            
        }        

        $this->trail = $trail;
        $this->getViewState()->set( self::STORAGE_PARAM_NAME, $trail );
    }    
    
    /**
     * reset the breadcrumb
     */
    public function reset()
    {
        $this->rewind();   
        $this->trail = array();
        
        $this->getViewState()->set( self::STORAGE_PARAM_NAME, array() );
    }
    
    /**
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->iterator = 0;
    }    

    /**
     * Get an element of the breadcrumb
     * @return Item
     */
    public function get($key)
    {
        $trail = $this->getTrail();        
        return $trail[$key];
    }        
    
    /**
     * @see Iterator::next()
     */
    public function next()
    {
        $this->iterator++;
    }

    /**
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->iterator;
    }
    
    /**
     * @see Iterator::valid()
     */
    public function valid()
    {
        $trail = $this->getTrail();
        
        return isset($trail[$this->iterator]);
    }
    
    /**
     * @see Iterator::current()
     */
    public function current()
    {
        $trail = $this->getTrail();
        
        return $trail[$this->iterator];
    }
    
    /**
     * 
     * @see Iterator::offsetExists($key)
     */
    public function offsetExists($key)
    {
        $trail = $this->getTrail();
        
        return isset($trail[$key]);
    }        

    /**
     *
     * @see Countable::count()
     * @return int the number of breadcrumb items
     */
    public function count()
    {
        $trail = $this->getTrail();
        
        return count($trail);
    }    

    /**
     * Crea una vista del breadcrumb.
     *
     * @return View La vista
     */
    public function createView()
    {                
        $view = new View();
        
        $view->set('theme', 'SnappmindsUtilsBreadcrumbBundle:Breadcrumb:breadcrumb.html.twig');
        $view->set('count', $this->count() );
        $view->set('hierarchySeparator',  self::HIERARCHY_SEPARATOR);
        $view->set('items', $this->getTrail() );

        return $view;
    }
    
    /**
     * 
     * @return string the rendered breadcrumb
     */
    public function render()
    {        
        $last = $this->count() -1; 
        $i = 0;
        
        $titles = array();
        
        foreach( $this as $item ){
            
            if( $i == $last){
                $titles[] = $item->getTitle();
            }else{
                $titles[] = "<a href='". $item->getUrl() ."'>" . $item->getTitle() . "</a>";
            }
            
            $i++;
        }
        return implode(self::HIERARCHY_SEPARATOR, $titles);
    }
    
    /**
     * 
     * @return string the rendered breadcrumb
     */
    public function __toString()
    {        
        return $this->render();
    }
    
}
?>

<?php

namespace Snappminds\Utils\Bundle\BluesBundle\Controller;

use Snappminds\Utils\Bundle\BreadcrumbBundle\Model\Item;
use Snappminds\Utils\Bundle\BluesBundle\Model\ViewState;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;


class Controller extends BaseController
{
    private $_viewState = null;
    private $_breadcrumb = null;
    
    /**
     * Obtiene el nombre del template del cual extenderá el abm.
     * 
     * Este template generalmente es el layout utilizado en la aplicación.
     * 
     * @return string Nombre del template según convención symfony. 
     */
    protected function getParentTemplateName()
    {
        return 'SnappmindsUtilsBluesBundle:Default:layout.html.twig';
    }    
    
    public function getDefaultViewParams()
    {
        return array( 'viewState' => $this->getViewState()->createView() );
    }     

     /*   
     * @return Snappminds\Utils\Bundle\BreadcrumbBundle\Model\Breadcrumb breadcrumb.
     */    
    public function getBreadcrumb(){
        
        if( is_null($this->_breadcrumb)  ){

            $viewState = $this->getViewState();
        
            $this->_breadcrumb = $this->get('snappminds_breadcrumb');   
            $this->_breadcrumb -> load( $viewState );
            
        }
        return $this->_breadcrumb;
    }
    
    function addBreadcrumbItem( $title, $routeData ){

        $bc = $this->getBreadcrumb()->add(  new Item( $title, $routeData ) );
    }
    
    /**
     * Renders a view.
     *
     * @param string   $view The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {        
        $parameters = array_merge( $parameters, $this->getDefaultViewParams() );
        return parent::render($view, $parameters, $response);
    }    
    
    /**
     * Obtiene el View State inicializados con los valores del request
     * @return ViewState 
     */
    
    public function getViewState()
    {
        if(is_null($this->_viewState) ){
            $this->_viewState = ViewState::createFromGlobals($this->getRequest(), $this->get('session'));
        }
        return $this->_viewState;
    }    
    
    public function internalRedirect($routeData, $status = 302)
    {
        $uniqid = uniqid();
        
        $params = array_merge( $routeData['params'], array( ViewState::REQUEST_PARAM_NAME => $uniqid ) );
        
        $this->get('session')->set($uniqid, $this->getViewState()->getData() );
        
        return parent::redirect( $this->generateUrl($routeData['route'], $params) , $status);
    }    
}

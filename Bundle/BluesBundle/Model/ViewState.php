<?php

namespace Snappminds\Utils\Bundle\BluesBundle\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Snappminds\Utils\Bundle\BluesBundle\View\View;

class ViewState
{
    private $data = array();
    
    const REQUEST_PARAM_NAME = "_viewstate";

    public function getData() {
        return $this->data;
    }
    
    protected function setData($data) {
        $this->data = $data;
    }    

    static function createFromGlobals(Request $request, Session $session){
        
        $viewState = new ViewState();
        
        $data = array();
        
        if( $request->request->has( self::REQUEST_PARAM_NAME ) ){
            $data = unserialize( urldecode( $request->request->get( self::REQUEST_PARAM_NAME )) );
        }elseif( $request->query->has( self::REQUEST_PARAM_NAME ) ){
            $data = $session->get( $request->query->get( self::REQUEST_PARAM_NAME ) );
            //$session->remove( $request->query->get( self::REQUEST_PARAM_NAME ) );
        }        
        
        $viewState->setData( $data );
        
        return $viewState;
    }
    
    public function get($key){

        $data = $this->getData();
        
        if (array_key_exists($key, $data )) {
            return $data[$key];
        }else{
            return null;
        }        
    }
    
    public function set($key, $value){

        $data = $this->getData();
        
        $data[$key] = $value;
        
        $this->setData($data);
    }    
    
    public function has($name)
    {
        $data = $this->getData();
        return array_key_exists($name, $data);
    }    
    /**
     * Crea una vista del breadcrumb.
     *
     * @return View La vista
     */
    public function createView()
    {        
        $view = new View();
        
        $view->set('requestParamName', self::REQUEST_PARAM_NAME);
        $view->set('data', urlencode( serialize( $this->getData()) ) );

        return $view;
    }    
}
<?php

namespace Snappminds\Utils\Bundle\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Request;

class AjaxTypeController extends Controller
{

    /**
     * Obtiene una respuesta ajax de un Type con soporte AJAX a partir de
     * un string.
     * 
     * El formato de la respuesta está establecido según el template indicado
     * en la opción ajaxresponse_template del Type.
     * 
     * Los datos que se pasan al template se obtienen de la variable 
     * ajaxresponse_data de la vista construida por el Type.
     * 
     * @param Request $request Requerimiento HTTP.
     * @param Type $ajaxEntityType Type del cual se obtendrán las opciones.
     * @param string $qbParamName Nombre del parámetro del QueryBuilder de los 
     * choices del type.
     * @param string $string Cadena que será el valor del parametro del Query
     * Builder.
     * @param int $minStringLength Longitud mínima que tiene que tener la
     * cadena para devolver información. 
     */
    public function ajaxResponseFromStringAction(Request $request, $ajaxEntityType, $qbParamName = null, $string = '', $minStringLength = 0)
    {
        if (strlen($string) >= $minStringLength) {
            if (!$qbParamName)
                $qbParamName = $ajaxEntityType->getName();

            $ajaxEntityType->setAttribute(
                    'query_builder_param_values', array(
                $qbParamName => $string
                    )
            );

            $view = $ajaxEntityType->getForm()->createView();

            $resultArray = $view->get('ajaxresponse_data');
        } else {
            $resultArray = array();
        }

        return $this->render($ajaxEntityType->getAttribute('ajaxresponse_template'), array(
                    'data' => $resultArray
                ));
    }

}

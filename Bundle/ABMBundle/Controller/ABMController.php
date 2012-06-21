<?php

namespace Snappminds\Utils\Bundle\ABMBundle\Controller;

use Snappminds\Utils\Bundle\WidgetsBundle\Widget\Grid\Grid;

use Snappminds\Utils\Bundle\BluesBundle\Model\ViewState;
use Snappminds\Utils\Bundle\BluesBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


abstract class ABMController extends Controller
{
    private $filterForm;   
    
    /**
     * Obtiene una instancia de la entidad sobre la que se está trabajando
     * 
     * @return Object Entidad sobre la que se está trabajando.
     */
    protected function getEntityInstance()
    {
        throw new \Exception('El método getEntityInstance debe ser redefinido en la clase concreta');
    }
    
    /**
     * Obtiene el nombre en singular de la entidad sobre la que se trabaja.
     * 
     * Ej. Persona, Materia, Institución
     * 
     * @return string Nombre en singular.
     */
    protected abstract function getSingularEntityName();
    
    /**
     * Obtiene el nombre en plural de la entidad sobre la que se trabaja.
     * 
     * Ej. Personas, Materias, Instituciones.
     * 
     * @return string Nombre en plural.
     */
    protected abstract function getPluralEntityName();        

    /**
     * Obtiene el titulo para el BrowseAction     
     * 
     * @return string.
     */    
    protected function getBrowseActionTitle(){
        return $this->getPluralEntityName();
    }

    /**
     * Obtiene el titulo para el InsertAction     
     * 
     * @return string.
     */    
    protected function getInsertActionTitle(){
        return "Alta de " . $this->getSingularEntityName();
    }

    /**
     * Obtiene el titulo para el UpdateAction     
     * 
     * @return string.
     */    
    protected function getUpdateActionTitle(){
        return "Actualización de " . $this->getSingularEntityName();
    }
    
    protected function getClassShortName()
    {
        $className = get_class($this);
        $className[strrpos($className, "\\")] = ":";
        $className = str_replace("\\", "", $className);
        $className = str_replace("Controller", "", $className);
        return $className;
    }
        
    /**
     * Obtiene el nombre del template utilizado para la vista de INSERT.
     * 
     * @return string Nombre del template según convención symfony. 
     */
    protected function getInsertTemplateName()
    {
        return 'SnappmindsUtilsABMBundle:ABM:insert.html.twig';
    }

    /**
     * Obtiene el nombre del template utilizado para la vista de UPDATE.
     * 
     * @return string Nombre del template según convención symfony. 
     */
    protected function getUpdateTemplateName()
    {
        return 'SnappmindsUtilsABMBundle:ABM:update.html.twig';
    }

    /**
     * Obtiene el nombre del template utilizado para la vista de BROWSE.
     * 
     * @return string Nombre del template según convención symfony. 
     */    
    protected function getBrowseTemplateName()
    {
        return 'SnappmindsUtilsABMBundle:ABM:browse.html.twig';
    }

    
    /**
     * Obtiene el nombre del template utilizado para mostrar el formulario.
     * 
     * @return string Nombre del template según convención symfony. 
     */    
    protected function getFormTemplateName()
    {
        return 'SnappmindsUtilsABMBundle:ABM:form.html.twig';
    }
    
    
    /**
     * Obtiene la información de la ruta a la vista BROWSE del ABM.
     * 
     * La información de la ruta consiste de un arreglo asociativo:
     * 'route' => Nombre de la ruta
     * 'params' => Arreglo asociativo con parámetros HTTP.
     * 
     * @return array Arreglo asociativo con información de la ruta.
     */
    protected abstract function getBrowseRouteData();
    
    /**
     * Obtiene la información de la ruta a la vista INSERT del ABM.
     * 
     * La información de la ruta consiste de un arreglo asociativo:
     * 'route' => Nombre de la ruta
     * 'params' => Arreglo asociativo con parámetros HTTP.
     * 
     * @return array Arreglo asociativo con información de la ruta.
     */
    protected function getInsertRouteData()
    {
        return null;
    }

    /**
     * Obtiene la información de la ruta a la vista UPDATE del ABM.
     * 
     * La información de la ruta consiste de un arreglo asociativo:
     * 'route' => Nombre de la ruta
     * 'params' => Arreglo asociativo con parámetros HTTP.
     * 
     * @return array Arreglo asociativo con información de la ruta.
     */
    protected function getUpdateRouteData()
    {
        return null;
    }

    /**
     * Obtiene la información de la ruta a la vista DELETE del ABM.
     * 
     * La información de la ruta consiste de un arreglo asociativo:
     * 'route' => Nombre de la ruta
     * 'params' => Arreglo asociativo con parámetros HTTP.
     * 
     * @return array Arreglo asociativo con información de la ruta.
     */
    protected function getDeleteRouteData()
    {
        return null;
    }
    
    public function getDefaultRouteParams(){
        return array();
    }  
        
    protected function getGrid()
    {        
        $browseRouteData = $this->getBrowseRouteData();        
        $updateRouteData = $this->getUpdateRouteData();
        $deleteRouteData = $this->getDeleteRouteData();
        
        $grid = $this->getGridInstance();

        if(is_null($grid->getRowsPerPage()) ){            
            $grid->setRowsPerPage( $this->container->getParameter('snappminds_utils_abm.grid.rows_per_page') );
        }

        $grid->setActionRoute($browseRouteData['route'] . "_data", $browseRouteData['params'] );
        $grid->setDefaultRouteParams( $this->getDefaultRouteParams() );
        
        if($updateRouteData && $grid->getActionColumn()->hasAction('update') ){
            $grid->setUpdateRoute($updateRouteData['route'], $updateRouteData['params']);
        }
            
        if($deleteRouteData && $grid->getActionColumn()->hasAction('delete') ){
            $grid->setdeleteRoute($deleteRouteData['route'], $deleteRouteData['params']);
        }        
        
        return $grid;
    }    
    
    public function browseAction(Request $request)
    {
        $this->addBreadcrumbItem($this->getBrowseActionTitle(), $this->getBrowseRouteData());
       
        $grid = $this->getGrid();
        
        $this->processFilterForm($this->getRequest(), $grid);
                
        $gridView = $grid->createView();                      

        return $this->renderBrowseView(
                array(
                    'grid' => $gridView,
                )
        );
    }

    public function browseDataAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {

            $grid = $this->getGrid();
            
            $this->processFilterForm($this->getRequest(), $grid);
            
            $gridView = $grid->createView();

            return $this->render(
                            $gridView->get('ajaxBrowseTemplate'), array('grid' => $gridView)
            );
        } else {
            throw $this->createNotFoundException();
        }
    }

    protected function getFormRedirectRouteData()
    {
        if ( $this->getBreadcrumb()->offsetExists( $this->getBreadcrumb()->count() - 2 ) ){
            $item = $this->getBreadcrumb()->get( $this->getBreadcrumb()->count() - 2 );
            $routeData = $item->getRouteData();    
        }else{
            $routeData = $this->getBrowseRouteData(); 
        }            
        return $routeData;
    }
    
    public function processForm($form, Request $request, $entity)
    {        
        $form->bindRequest($request);

        if ($form->isValid()) {
        
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();
            
            return true;
        }else{
            return false;
        }
    }
    
    public function insertAction(Request $request)
    {       
        $entity = $this->getEntityInstance();

        $form = $this->createForm($this->getForm(), $entity);
                        
        if ($request->request->has('_form_method') && ($request->request->get('_form_method') == 'POST')) {

            $valid = $this->processForm($form, $request, $entity);
            
            if ($valid) {                
                return $this->internalRedirect( $this->getFormRedirectRouteData() );
            } else {
                return $this->renderInsertView(array('form' => $form->createView()));
            }            
        }
        
        $this->addBreadcrumbItem($this->getInsertActionTitle(), $this->getInsertRouteData());

        return $this->renderInsertView( array('form' => $form->createView()));        
    }
    
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getDoctrine()->getRepository( $this->getRepositoryName() )->find($id);

        $form = $this->createForm($this->getForm(), $entity);

        if ($request->request->has('_form_method') && ($request->request->get('_form_method') == 'POST')) {
            $valid = $this->processForm($form, $request, $entity);
            
            if ($valid) {                
                return $this->internalRedirect( $this->getFormRedirectRouteData() );
            } else {
                return $this->renderUpdateView(array('form' => $form->createView()));
            }
        }
        
        $this->addBreadcrumbItem($this->getUpdateActionTitle(), $this->getUpdateRouteData());

        return $this->renderUpdateView(array('form' => $form->createView()));
    }
    
    public function deleteAction(Request $request, $id)
    {
        if (!($request->getMethod() == 'POST'))
            throw $this->createNotFoundException();

        $entity = $this->getDoctrine()
                ->getRepository($this->getRepositoryName())
                ->find($id);

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($entity);
        $em->flush();
            
        return $this->internalRedirect( $this->getDeleteActionRedirectRouteData() );
    }    

    /**
     * Devuelve el par (ruta, params) a procesar luego de un deleteAction 
     */    
    protected function getDeleteActionRedirectRouteData()
    {
        if ( $this->getBreadcrumb()->offsetExists( $this->getBreadcrumb()->count() - 1 ) ){
            $item = $this->getBreadcrumb()->get( $this->getBreadcrumb()->count() - 1 );
            $routeData = $item->getRouteData();    
        }else{
            $routeData = $this->getBrowseRouteData(); 
        }            
        return $routeData;
    }
    
    /**
     * Procesa el formulario de filtro y establece el criterio de la
     * grilla.
     * 
     * TODO: Notar que el parámetro grid no sería necesario si se implementa
     * un singleton como en getFilterForm.
     * 
     * @param Request $request Requerimiento HTTP
     * @param Grid $grid Grilla del abm
     */
    public function processFilterForm(Request $request, Grid $grid)
    {       
        $filterForm = $this->getFilterForm();
     
        if ($filterForm) {
            
            if ($request->query->get($filterForm->getName())){
                
                $filterForm->bindRequest($this->getRequest());

                if ($filterForm->isValid()) {                    
                    $grid->setCriteria($filterForm->getData());
                    $actionRoute = $grid->getActionRoute();
                    $actionRoute['params'][$filterForm->getName()] = $request->query->get($filterForm->getName());
                    $grid->setActionRoute($actionRoute['route'], $actionRoute['params']);                    
                }
                
            }            
        }                 
    }
    
    /**
     * Devuelve la instancia del formulario de filtro del abm.
     * 
     * Este método debe ser redefinido para construir un formulario de filtro.
     * No se debe utilizar este método para obtener el formulario. 
     * (ver getFilterForm).
     * 
     * @return FormType Formulario de filtro.
     */
    protected function getFilterFormInstance()
    {
        return null;
    }
    
    /**
     * Devuelve el formulario de filtro para este abm.
     * 
     * Este método utiliza el patrón Singleton para obtener siempre
     * la misma instancia del formulario.
     * La construcción de la instancia se define en getFilterFormInstance.
     * 
     * @return FormType Formulario de filtro.
     */
    protected function getFilterForm()
    {
        if (!$this->filterForm)
            $this->filterForm = $this->getFilterFormInstance();
        
        return $this->filterForm;
    }

    protected function renderInsertView(array $params)
    {
        return $this->render(
                $this->getInsertTemplateName(), 
                array_merge(
                    $params, 
                    $this->getInsertViewParams()
                )
        );
    }
    
    protected function getInsertViewParams()
    {
        return array(
            'parentTemplateName' => $this->getParentTemplateName(),
            'singularEntityName' => $this->getSingularEntityName(),
            'title' => $this->getInsertActionTitle(),
            'formTemplateName' => $this->getFormTemplateName(),
            'returnRouteData' => $this->getBrowseRouteData(),
            'breadcrumb' => $this->getBreadcrumb()->createView()
        );
    }

    protected function renderUpdateView(array $params)
    {
        return $this->render(
                $this->getUpdateTemplateName(), 
                array_merge(
                    $params, 
                    $this->getUpdateViewParams()
                )
        );
    }
    
    protected function getUpdateViewParams()
    {
        return array(
            'parentTemplateName' => $this->getParentTemplateName(),
            'singularEntityName' => $this->getSingularEntityName(),
            'title' => $this->getUpdateActionTitle(),
            'formTemplateName' => $this->getFormTemplateName(),
            'returnRouteData' => $this->getBrowseRouteData(),
            'breadcrumb' => $this->getBreadcrumb()->createView()
        );
    }
    
    protected function renderBrowseView(array $params)
    {
        return $this->render(
                $this->getBrowseTemplateName(), 
                array_merge(
                    $params, 
                    $this->getBrowseViewParams()
                )
        );
    }    
    
    protected function getBrowseViewParams()
    {
        $params = array(
                'parentTemplateName' => $this->getParentTemplateName(),
                'pluralEntityName' => $this->getPluralEntityName(),
                'title' => $this->getBrowseActionTitle(),
                'defaultRouteParams' => $this->getDefaultRouteParams(),
                'breadcrumb' => $this->getBreadcrumb()->createView(),
                );
        
        $insertRouteData = $this->getInsertRouteData(); 
        if ( $insertRouteData ) {
            $params['insertRouteName'] = $insertRouteData['route'];
            $params['insertRouteParams'] = $insertRouteData['params'];
        }
        
        if ($this->getFilterForm()) {
            $browseRouteData = $this->getBrowseRouteData();
            $params['browseRouteName'] = $browseRouteData['route'];            
            $params['browseRouteParams'] = $browseRouteData['params'];            
            $params['filterForm'] = $this->getFilterForm()->createView();            
        }                
        
        return $params;        
    }
         
}

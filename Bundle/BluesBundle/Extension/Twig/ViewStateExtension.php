<?php

namespace Snappminds\Utils\Bundle\BluesBundle\Extension\Twig;

use Symfony\Component\Form\FormView;
use Snappminds\Utils\Bundle\BluesBundle\View\View;

class ViewStateExtension extends \Twig_Extension
{
    private $environment;
    private $theme;
    private $template;

    public function getName()
    {
        return 'viewstate';
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'viewstate_link' => new \Twig_Function_Method($this, 'renderViewStateLink', array('is_safe' => array('html'))),
            'viewstate_form_hidden' => new \Twig_Function_Method($this, 'renderViewStateHidden', array('is_safe' => array('html'))),
            'viewstate_redirect' => new \Twig_Function_Method($this, 'renderViewStateRedirect', array('is_safe' => array('html'))),
        );
    }

    public function renderViewStateRedirect($url)
    {       
        $html = $this->renderBlock(
                                    'viewStateRedirect', 
                                    array(
                                            'url' => $url,
                                    ),
                                    'SnappmindsUtilsBluesBundle:ViewState:viewState.html.twig'
                            );
      
        return $html;
    }
    
    public function renderViewStateLink($title, $url, $class = null)
    {       
        $html = $this->renderBlock(
                                    'viewStateLink', 
                                    array(
                                            'title' => $title,
                                            'url' => $url,
                                            'class' => $class
                                    ),
                                    'SnappmindsUtilsBluesBundle:ViewState:viewState.html.twig'
                            );
      
        return $html;
    }

    public function renderViewStateHidden( FormView $view )
    {       
        $html = $this->renderBlock(
                                    'viewStateHidden', 
                                    array(
                                            'formView' => $view,
                                    ),
                                    'SnappmindsUtilsBluesBundle:ViewState:viewState.html.twig'
                            );
      
        return $html;
    }

    /**
     * Busca un bloque en un template.
     * Si el template hereda de otro, se busca también en los padres.
     * @param string Nombre del bloque.
     * @param string Nombre del template.
     */
    protected function hasBlock($name, $theme)
    {
        $template = $this->getTemplate($theme);
        $result = false;

        while ($template && !$result) {
            $result = $template->hasBlock($name);
            $template = $template->getParent(array());
        }

        return $result;
    }

    protected function getTemplate($theme)
    {
        if ($this->theme != $theme) {
            $this->template = null;
            $this->theme = $theme;
        }

        if (null === $this->template)
            $this->template = $this->environment->loadTemplate($theme);

        return $this->template;
    }

    protected function renderBlock($name, $parameters, $theme)
    {
        $template = $this->getTemplate($theme);
        return $template->renderBlock($name, $parameters);
    }

}

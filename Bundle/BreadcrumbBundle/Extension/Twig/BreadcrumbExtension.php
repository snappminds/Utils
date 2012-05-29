<?php

namespace Snappminds\Utils\Bundle\BreadcrumbBundle\Extension\Twig;

use Snappminds\Utils\Bundle\BreadcrumbBundle\Model\Breadcrumb;
use Snappminds\Utils\Bundle\BluesBundle\View\View;

class BreadcrumbExtension extends \Twig_Extension
{
    private $environment;
    private $theme;
    private $template;

    public function getName()
    {
        return 'breadcrumb';
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
            'breadcrumb_widget' => new \Twig_Function_Method($this, 'renderBreadcrumb', array('is_safe' => array('html'))),
        );
    }

    public function renderBreadcrumb(View $breadcrumb)
    {

        $html = $this->renderBlock(
                                    'breadcrumb', 
                                    array(
                                            'breadcrumb' => $breadcrumb
                                    ),
                                    $breadcrumb->get('theme')
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

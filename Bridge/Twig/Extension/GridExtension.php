<?php

namespace Snappminds\Utils\Bridge\Twig\Extension;

use Snappminds\Utils\Bundle\WidgetsBundle\View\View;

class GridExtension extends \Twig_Extension {

    private $environment;
    private $theme;
    private $template;

    public function __construct() {
        
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment) {
        $this->environment = $environment;
    }

    public function getFunctions() {
        return array(
            'grid_widget' => new \Twig_Function_Method($this, 'renderWidget', array('is_safe' => array('html'))),
            'grid_headers' => new \Twig_Function_Method($this, 'renderHeaders', array('is_safe' => array('html'))),
            'grid_data' => new \Twig_Function_Method($this, 'renderData', array('is_safe' => array('html'))),
            'grid_cell' => new \Twig_Function_Method($this, 'renderCell', array('is_safe' => array('html')))
        );
    }

    public function renderWidget(View $grid) {
        $html = $this->renderBlock(
                'grid', array(
            'grid' => $grid
                ), $grid->get('theme')
        );

        return $html;
    }

    public function renderHeaders(View $grid) {

        $html = $this->renderBlock(
                'headers', array(
            'grid' => $grid
                ), $grid->get('theme')
        );

        return $html;
    }

    public function renderData(View $grid) {

        $html = $this->renderBlock( 'data', array( 'grid' => $grid ), $grid->get('theme') );

        return $html;
    }

    public function renderCell(View $column, $value, View $grid) {

        $blockName = $column->get('name') . '_' . $column->get('type') . '_cell';

        if (!$this->hasBlock($blockName, $grid->get('theme'))) {
            $blockName = $column->get('type') . '_cell';
        }

        if (!$this->hasBlock($blockName, $grid->get('theme'))) {
            $blockName = 'cell';
        }

        return $this->renderBlock(
                        $blockName, array(
                    'column' => $column,
                    'value' => $value,
                    'grid' => $grid
                        ), $grid->get('theme')
        );
    }

    public function getName() {
        return 'grid';
    }

    /**
     * Busca un bloque en un template.
     * Si el template hereda de otro, se busca también en los padres.
     * @param string Nombre del bloque.
     * @param string Nombre del template.
     */
    protected function hasBlock($name, $theme) {
        $template = $this->getTemplate($theme);
        $result = false;

        while ($template && !$result) {
            $result = $template->hasBlock($name);
            $template = $template->getParent(array());
        }

        return $result;
    }

    protected function getTemplate($theme) {
        if ($this->theme != $theme) {
            $this->template = null;
            $this->theme = $theme;
        }

        if (null === $this->template)
            $this->template = $this->environment->loadTemplate($theme);

        return $this->template;
    }

    protected function renderBlock($name, $parameters, $theme) {
        $template = $this->getTemplate($theme);

        return $template->renderBlock($name, $parameters);
    }

}

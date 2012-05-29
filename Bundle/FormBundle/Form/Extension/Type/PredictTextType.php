<?php

namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\Form\FormInterface;


/**
 * 
 *
 * @author gcaseres
 */
class PredictTextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'predicttext';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['data_route'] && !is_array($options['data_route'])) {
            throw new FormException('El "data_route" Debe ser un arreglo asociativo con claves: route y params".');
        }

        $builder
                ->setAttribute('data_route', $options['data_route'])
                ->setAttribute('data_param', $options['data_param'])
        ;        
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        $view
                ->set('data_route', $form->getAttribute('data_route'))
                ->set('data_param', $form->getAttribute('data_param'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_route' => null,
            'data_param' => 'data',
        );
    }
}


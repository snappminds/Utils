<?php

namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\Form\FormInterface;
use \Symfony\Bridge\Doctrine\RegistryInterface;
use \Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;

use Snappminds\Utils\Bundle\FormBundle\Form\Extension\DataTransformer\EntityToIdTransformer;

/**
 * 
 *
 * @author gcaseres
 */
class EntityPredictTextType extends AbstractType
{
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->setRegistry($registry);
    }

    protected function setRegistry(RegistryInterface $value)
    {
        $this->registry = $value;
    }

    protected function getRegistry()
    {
        return $this->registry;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'predicttext';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entitypredicttext';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('query_builder', $options['query_builder']);
        $builder->setAttribute('class', $options['class']);
        $builder->setAttribute('em', $options['em']);
        $builder->setAttribute('property', $options['property']);
        $builder->setAttribute('query_builder_param_values', $options['query_builder_param_values']);
        $builder->setAttribute('ajaxresponse_template', $options['ajaxresponse_template']);
        
/*
        $builder->prependClientTransformer(
                new EntityToIdTransformer(
                        $this->getRegistry()->getEntityManager($options['em']),
                        $options['class']
                )
        );*/
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        if ($form->hasAttribute('query_builder_param_values')) {
            $qb = $form->getAttribute('query_builder');
            $qbParams = $form->getAttribute('query_builder_param_values');

            foreach ($qbParams as $name => $value) {
                $qb->setParameter($name, $value);
            }
            

            $choices = new EntityChoiceList(
                            $this->getRegistry()->getEntityManager($form->getAttribute('em')),
                            $form->getAttribute('class'),
                            $form->getAttribute('property'),
                            $qb
            );


            $view->set('ajaxresponse_data', $choices->getChoices());
        }
        
        //$view->set('full_name', $view->get('full_name').'[]');
        

    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em' => null,
            'class' => null,
            'property' => null,
            'query_builder' => null,
            'query_builder_param_values' => null,
            'ajaxresponse_template' => 'SnappmindsUtilsFormBundle:PredictText:ajax.html.twig'
        );

        $options = array_replace($defaultOptions, $options);

        return $defaultOptions;
    }

}


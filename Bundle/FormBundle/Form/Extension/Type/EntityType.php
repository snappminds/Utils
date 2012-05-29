<?php

namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\Form\FormInterface;
use \Symfony\Bridge\Doctrine\RegistryInterface;
use \Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use \Symfony\Component\Form\Util\PropertyPath;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntityToIdTransformer as SymfonyEntityToIdTransformer;
use Snappminds\Utils\Bundle\FormBundle\Form\Extension\DataTransformer\EntityToIdTransformer;

/**
 * 
 *
 * @author gcaseres
 */
class EntityType extends AbstractType
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

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('query_builder', $options['query_builder']);
        $builder->setAttribute('class', $options['class']);
        $builder->setAttribute('em', $options['em']);
        $builder->setAttribute('property', $options['property']);
        $builder->setAttribute('query_builder_param_values', $options['query_builder_param_values']);
        $builder->setAttribute('ajaxresponse_template', $options['ajaxresponse_template']);

        if (!is_null($options['data_route'])) {

            $builder->prependClientTransformer(
                    new EntityToIdTransformer(
                            $this->getRegistry()->getEntityManager($options['em']),
                            $options['class']
                    )
            );
        } else {
            if ($options['multiple']) {
                $builder
                        ->addEventSubscriber(new MergeCollectionListener())
                        ->prependClientTransformer(new EntitiesToArrayTransformer($options['choice_list']))
                ;
            } else {
                $builder->prependClientTransformer(new SymfonyEntityToIdTransformer($builder->getAttribute('choice_list')));
            }
        }
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

            $choices = $choices->getChoices();



            $view->set('ajaxresponse_data', $choices);
        }

        if ($form->getData()) {
            if ($form->hasAttribute('property')) {
                $property = new PropertyPath($form->getAttribute('property'));
                $view->set('value_label', $property->getValue($form->getData()));
            } else {
                if (!method_exists($form->getData(), '__toString')) {
                    throw new FormException('Entities passed to the choice field must have a "__toString()" method defined (or you can also override the "property" option).');
                }
                $view->set('value_label', (string) $form->getData());
            }
        } else {
            $view->set('value_label', null);
        }
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em' => null,
            'class' => null,
            'property' => null,
            'query_builder' => null,
            'choices' => null,
            'query_builder_param_values' => null,
            'ajaxresponse_template' => 'SnappmindsUtilsFormBundle:Choice:ajax.html.twig'
        );

        $defaultOptions = array_replace($defaultOptions, $options);

        if (!isset($options['choice_list'])) {
            $defaultOptions['choice_list'] = new EntityChoiceList(
                            $this->registry->getEntityManager($defaultOptions['em']),
                            $defaultOptions['class'],
                            $defaultOptions['property'],
                            $defaultOptions['query_builder'],
                            $defaultOptions['choices']
            );
        }




        return $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity';
    }

}


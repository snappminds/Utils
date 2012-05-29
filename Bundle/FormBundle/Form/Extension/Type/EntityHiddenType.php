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
class EntityHiddenType extends AbstractType
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

        $builder->setAttribute('class', $options['class']);
        $builder->setAttribute('em', $options['em']);
        

        $builder->prependClientTransformer(
                new EntityToIdTransformer(
                        $this->getRegistry()->getEntityManager($options['em']),
                        $options['class']
                )
        );
    }

    public function buildView(FormView $view, FormInterface $form)
    {        
        
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em' => null,
            'class' => null,
        );

        $options = array_replace($defaultOptions, $options);

        return $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entityhidden';
    }

}


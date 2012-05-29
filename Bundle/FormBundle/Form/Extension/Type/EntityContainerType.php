<?php

namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\Form\FormInterface;
use \Symfony\Bridge\Doctrine\RegistryInterface;
use \Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use \Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Exception\FormException;

use Snappminds\Utils\Bundle\FormBundle\Form\Extension\DataTransformer\EntityToIdTransformer;

/**
 * 
 *
 * @author gcaseres
 */
class EntityContainerType extends AbstractType
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
        $builder->setAttribute('property', $options['property']);
        

        $builder->prependClientTransformer(
                new EntityToIdTransformer(
                        $this->getRegistry()->getEntityManager($options['em']),
                        $options['class']
                )
        );
    }

    public function buildView(FormView $view, FormInterface $form)
    {       
        if ($form->getAttribute('property')) {
            $property = new PropertyPath($form->getAttribute('property'));
            $view->set('display_value', $property->getValue($form->getData()));
        } else {            
            if (!method_exists($form->getData(), '__toString')) {
                throw new FormException('Entities passed to the choice field must have a "__toString()" method defined (or you can also override the "property" option).');
            }
            $view->set('display_value', (string)$form->getData());
        }
        
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em' => null,
            'class' => null,
            'property' => null
        );

        $options = array_replace($defaultOptions, $options);

        return $defaultOptions;
    }

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
        return 'entitycontainer';
    }

}


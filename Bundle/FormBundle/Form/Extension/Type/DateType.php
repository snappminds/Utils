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
class DateType extends \Symfony\Component\Form\Extension\Core\Type\DateType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setAttribute('yearRange', $options['yearRange']);
        $builder->setAttribute('changeMonth', $options['changeMonth']);
        $builder->setAttribute('changeYear', $options['changeYear']);
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = parent::getDefaultOptions($options);

        $defaultOptions['yearRange'] = null;
        $defaultOptions['changeMonth'] = true;
        $defaultOptions['changeYear'] = false;

        return $defaultOptions;
    }

    public function buildViewBottomUp(FormView $view, FormInterface $form)
    {
        parent::buildViewBottomUp($view, $form);

        $view->set('yearRange', $form->getAttribute('yearRange'));
        $view->set('changeMonth', $form->getAttribute('changeMonth'));
        $view->set('changeYear', $form->getAttribute('changeYear'));
    }

}


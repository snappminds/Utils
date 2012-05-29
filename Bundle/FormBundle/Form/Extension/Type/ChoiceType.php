<?php

namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormInterface;
use \Symfony\Component\Form\Exception\FormException;
use \Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;
use \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use \Symfony\Component\Form\Extension\Core\EventListener\FixRadioInputListener;
use \Symfony\Component\Form\FormView;
use \Symfony\Component\Form\Extension\Core\DataTransformer\ScalarToChoiceTransformer;
use \Symfony\Component\Form\Extension\Core\DataTransformer\ScalarToBooleanChoicesTransformer;
use \Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToChoicesTransformer;
use \Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToBooleanChoicesTransformer;
use Snappminds\Utils\Bundle\FormBundle\Form\Extension\DataTransformer\EntityToIdTransformer;

/**
 * 
 *
 * @author gcaseres
 */
class ChoiceType extends AbstractType
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
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['choice_list'] && !$options['choice_list'] instanceof ChoiceListInterface) {
            throw new FormException('The "choice_list" must be an instance of "Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface".');
        }

        if (!$options['choice_list']) {
            $options['choice_list'] = new ArrayChoiceList($options['choices']);
        }

        if ($options['expanded']) {
            // Load choices already if expanded
            $options['choices'] = $options['choice_list']->getChoices();

            foreach ($options['choices'] as $choice => $value) {
                if ($options['multiple']) {
                    $builder->add((string) $choice, 'checkbox', array(
                        'value' => $choice,
                        'label' => $value,
                        // The user can check 0 or more checkboxes. If required
                        // is true, he is required to check all of them.
                        'required' => false,
                    ));
                } else {
                    $builder->add((string) $choice, 'radio', array(
                        'value' => $choice,
                        'label' => $value,
                    ));
                }
            }
        }

        // empty value
        if ($options['multiple'] || $options['expanded']) {
            // never use and empty value for these cases
            $emptyValue = null;
        } elseif (false === $options['empty_value']) {
            // an empty value should be added but the user decided otherwise
            $emptyValue = null;
        } elseif (null === $options['empty_value']) {
            // user did not made a decision, so we put a blank empty value
            $emptyValue = $options['required'] ? null : '';
        } else {
            // empty value has been set explicitly
            $emptyValue = $options['empty_value'];
        }

        $builder
                ->setAttribute('choice_list', $options['choice_list'])
                ->setAttribute('preferred_choices', $options['preferred_choices'])
                ->setAttribute('multiple', $options['multiple'])
                ->setAttribute('expanded', $options['expanded'])
                ->setAttribute('required', $options['required'])
                ->setAttribute('empty_value', $emptyValue)
        ;

        if ($options['expanded']) {
            if ($options['multiple']) {
                $builder->appendClientTransformer(new ArrayToBooleanChoicesTransformer($options['choice_list']));
            } else {
                $builder
                        ->appendClientTransformer(new ScalarToBooleanChoicesTransformer($options['choice_list']))
                        ->addEventSubscriber(new FixRadioInputListener(), 10)
                ;
            }
        } else {
            if ($options['multiple']) {
                $builder->appendClientTransformer(new ArrayToChoicesTransformer());
            } else {
                $builder->appendClientTransformer(new ScalarToChoiceTransformer());
            }
        }

        if ($options['data_route'] && !is_array($options['data_route'])) {
            throw new FormException('El "data_route" Debe ser un arreglo asociativo con claves: route y params".');
        }

        if ($options['data_route'] == '') {
            $options['data_route'] = null; // Hay algun problemita ya que cuando no se lo seteamos en lugar de tener null tiene ''
        }

        $builder
                ->setAttribute('data_route', $options['data_route'])
                ->setAttribute('data_param', $options['data_param'])
                ->setAttribute('editable', $options['editable'])
        ;
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        if (( is_null($form->hasAttribute('data_route'))) || ($form->getAttribute('data_route') == '' )) {
            $choices = $form->getAttribute('choice_list')->getChoices();
        } else {
            $choices = array();
        }

        $preferred = array_flip($form->getAttribute('preferred_choices'));

        $view
                ->set('multiple', $form->getAttribute('multiple'))
                ->set('expanded', $form->getAttribute('expanded'))
                ->set('preferred_choices', array_intersect_key($choices, $preferred))
                ->set('choices', array_diff_key($choices, $preferred))
                ->set('separator', '-------------------')
                ->set('empty_value', $form->getAttribute('empty_value'))
        ;

        if ($view->get('multiple') && !$view->get('expanded')) {
            // Add "[]" to the name in case a select tag with multiple options is
            // displayed. Otherwise only one of the selected options is sent in the
            // POST request.
            $view->set('full_name', $view->get('full_name') . '[]');
        }

        if (!$view->has('value_label')) 
            $view->set('value_label', null);


        $view
                ->set('is_ajax', !is_null($form->getAttribute('data_route')))
                ->set('data_route', $form->getAttribute('data_route'))
                ->set('data_param', $form->getAttribute('data_param'))
                ->set('editable', $form->getAttribute('editable'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {

        $multiple = isset($options['multiple']) && $options['multiple'];
        $expanded = isset($options['expanded']) && $options['expanded'];

        return array(
            'multiple' => false,
            'expanded' => false,
            'choice_list' => null,
            'choices' => array(),
            'preferred_choices' => array(),
            'empty_data' => $multiple || $expanded ? array() : '',
            'empty_value' => $multiple || $expanded || !isset($options['empty_value']) ? null : '',
            'error_bubbling' => false,
            'data_route' => null,
            'data_param' => 'data',
            'editable' => false
        );
    }

}


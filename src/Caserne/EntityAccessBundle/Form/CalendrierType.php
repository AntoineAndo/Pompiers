<?php

namespace Caserne\EntityAccessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendrierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder ↔
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('idGarde')
            ->add('idPompier')
            ->add('dispo', "choice", array(
                'choices' => array(
                    'Disponible' => "disponible",
                    'Astreinte' => "astreinte",
                    'Urgence' => "urgence",
                    'Indisponible' => "indisponible"
                )
            )
        );
    }

    public function getInputType($fieldType)
    {
        switch ($fieldType) {
            case 'integer':
                $inputType = 'number';
                break;
            case 'float':
                $inputType = 'number';
                break;
            default:
                $inputType = 'text';
        }
        return $inputType;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'fields_name' => null,
            'csrf_protection' => false,
            'entityName' => null,
            'entity' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return null;
    }
}

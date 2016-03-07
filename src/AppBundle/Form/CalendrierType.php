<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Date;

class CalendrierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idGarde')
            ->add('idPompier')
            ->add('dispo', 'choice', [
                'choices' => [
                    'Garde' => 'garde',
                    'Astreinte' => 'astreinte',
                    'Urgence' => 'urgence',
                    'Indisponible' => 'indisponible'
                ],
                'choices_as_values' => false])
            ->add('valide');
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Calendrier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_calendrier';
    }
}

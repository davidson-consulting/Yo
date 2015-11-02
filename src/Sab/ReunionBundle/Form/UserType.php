<?php

namespace Sab\ReunionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type => user
 * 
 */


class UserType extends AbstractType {

    /**
     * Formulaire d'ajout d'événement
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('date_debut_event', 'datetime', array('required' => true, 'widget' => 'single_text' ))
                ->add('date_fin_event', 'datetime', array('required' => true, 'widget' => 'single_text'))
                ->add('username', 'text')
                ->add('password', 'password');
    }

    /**
     * setDefaultOptions
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Sab\ReunionBundle\Entity\User'
        ));
    }

    /**
     * Nom du formulaire
     * @return string
     */
    public function getName() {
        return 'sab_reunionbundle_user';
    }

}

<?php

namespace Sab\ReunionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type => Thème
 * 
 */

class ThemeType extends AbstractType {

    /**
     * Formulaire d'ajout de thème a un événement
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fileProfilPicture', 'file', array(
                    'required' => false
                ))
                ->add('fileLogo', 'file', array(
                    'required' => false
                ))
                ->add('fileBackground', 'file', array(
                    'required' => false
                ))
        ;
    }

    /**
     * setDefaultOptions
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Sab\ReunionBundle\Entity\Theme'
        ));
    }

    /**
     * Nom du formulaire
     * @return string
     */
    public function getName() {
        return 'sab_reunionbundle_theme';
    }

}

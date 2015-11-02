<?php

namespace Sab\ReunionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type => Question
 *
 * 
 */
class QuestionType extends AbstractType {

    /**
     * Formulaire pour l'ajout des questions
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('contenu', 'textarea', array(
                    'required' => true,
                ))
                ->add('auteur', 'text', array(
                    'required' => false,
                ))
                ->add('Envoyer', 'submit', array('attr' => array('class' => 'envoyer')));
    }

    /**
     * setDefaultOptions
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Sab\ReunionBundle\Entity\Question'
        ));
    }
    
    /**
     * Nom du formulaire
     * @return string
     */
    public function getName() {
        return "sab_reunion_bundle_question_type";
    }

}

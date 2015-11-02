<?php

namespace Sab\ReunionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type => Event
 * 
 */
class EventType extends AbstractType {

    /**
     * Formulaire d'ajout des événements
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('createur', 'text')
                ->add('labelEvent', 'text')
                ->add('description', 'textarea')
                ->add('userUser', new UserType())
                ->add('theme', new ThemeType())
        ;
    }

    /**
     * setDefaultOptions
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Sab\ReunionBundle\Entity\Event',
            'cascade_validation' => true,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention' => 'task_item'
        ));
    }

    /**
     * Nom du formulaire
     * @return string
     */
    public function getName() {
        return 'sab_reunionbundle_event';
    }

}

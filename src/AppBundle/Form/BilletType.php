<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BilletType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nowYear = (int) date('Y');
        $builder->add('dateVisite', DateType::class, array(
            'years' => array( $nowYear, $nowYear+1, $nowYear+2, $nowYear+3)
        ))
            ->add('nom')
            ->add('prenom')
            ->add('pays', CountryType::class, array(
                'data' => 'FR'
            ))
            ->add('dateNaissance', BirthdayType::class)
            ->add('type',ChoiceType::class, array(
                    'choices'  => array(
                        'Journée' => 'journee',
                        'Demi-journée' => 'demi-journee',
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'data' => "journee"
                )
            )
            ->add('tarifReduit', CheckboxType::class, array('required' => false))
            ->add('commande', CommandeType::class)
            ->add('Suivant', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Billet'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_billet';
    }


}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nowYear = (int) date('Y');
        $date = new \DateTime();
        $builder->add('dateVisite', DateType::class, array(
                'years' => array( $nowYear, $nowYear+1, $nowYear+2, $nowYear+3),
                'data' => $date
            ))
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
            ->add('email', EmailType::class)
            ->add('nombreBillets', IntegerType::class, array('attr' => array('min' =>  1)))
            ->add('billets', CollectionType::class, array(
                'entry_type' => BilletType::class,
                'allow_add' => true
            ))
            ->add('Suivant', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_commande';
    }


}

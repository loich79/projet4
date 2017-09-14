<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CommandeDeuxiemePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('nombreBillets')
            ->remove('dateVisite')
            ->remove('type')
            ->remove('billets')
            ->add('billets', CollectionType::class, array(
                'entry_type' => BilletType::class,
                'allow_add' => true
            ))
            ->remove('Suivant')
            ->add('paiement', SubmitType::class);
    }
    public function getParent()
    {
        return CommandeType::class;
    }
}

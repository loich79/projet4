<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BilletDeuxiemePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('type')
            ->remove('tarif')
            ->remove('dateVisite')
            ->remove('commande')->remove('Suivant');
    }
    public function getParent()
    {
        return BilletType::class;
    }
}
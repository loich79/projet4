<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BilletPremierePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('nom')
            ->remove('prenom')
            ->remove('tarif')
            ->remove('pays')
            ->remove('dateNaissance')
            ->remove('commande')
            ->add('commande', CommandePremierePageType::class);
    }
    public function getParent()
    {
        return BilletType::class;
    }
}
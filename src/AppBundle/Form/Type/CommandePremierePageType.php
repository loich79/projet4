<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommandePremierePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('email')
            ->remove('billets');
    }
    public function getParent()
    {
        return CommandeType::class;
    }
}

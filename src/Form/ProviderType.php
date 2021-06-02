<?php

namespace App\Form;

use App\Entity\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, array('label' => 'Provider Code : '))
            ->add('name', TextType::class, array('label' => 'Name : '))
            ->add('responsible', TextType::class, array('label' => 'Responsible : '))
            ->add('address', TextType::class, array('label' => 'Address : '))
            ->add('city', TextType::class, array('label' => 'City : '))
            ->add('phone', TelType::class, array('label' => 'Phone : '))
            ->add('email', TextType::class, array('label' => 'Email : '))
            ->add('taxRegNumber', TextType::class, array('label' => 'Tax registration number:'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}

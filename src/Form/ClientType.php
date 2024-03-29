<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class,
                [
                    'label' => 'Client Code : '
                ]
            )
            ->add('name', TextType::class,
                [
                    'label' => 'Clien name : '
                ]
            )
            ->add('responsible', TextType::class,
                [
                    'label' => 'Responsible  :'
                ]
            )
            ->add('address', TextType::class,
                [
                    'label' => 'Client Address :'
                ]
            )
            ->add('city', TextType::class,
                [
                    'label' => 'City :  '
                ]
            )
            ->add('phone', TextType::class,
                [
                    'label' => 'Phone : '
                ]
            )
            ->add('email', TextType::class,
                [
                    'label' => 'Email :'
                ]
            )
            ->add('taxRegNumber', TextType::class,
                [
                    'label' => 'TAX registration number : '
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}

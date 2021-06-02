<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Name : '])
            ->add('buyingPrice', TextType::class, ['label' => 'Buying price : '])
            ->add('sellingPrice', TextType::class, ['label' => 'Selling price : '])
            ->add('vat', TextType::class, ['label' => 'VAT  : '])
            ->add('stock', NumberType::class, ['label' => 'Stock : '])
            ->add('image', FileType::class, ['label' => 'Image : '])
            ->add('category', EntityType::class,
                [
                    'label' => 'Category : ',
                    'class' => Category::class,
                    'choice_label' => 'name',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

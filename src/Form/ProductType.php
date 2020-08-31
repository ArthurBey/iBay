<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\ImageType;
use App\Entity\Category;
use App\Entity\Condition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'article',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix unitaire',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('shippingCost', MoneyType::class, [
                'label' => 'Frais d\'expédition',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Quantité en vente',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('details', TextareaType::class, [
                'label' => 'Ecrivez une présentation du produit',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('coverUrl', UrlType::class, [
                'label' => 'Photo de couverture',
                'attr' => [
                    'placeholder' => ''
                ]
            ])
            ->add('category', EntityType::class,
            [ 
              'class' => Category::class,
              'choice_label' => 'title',
              'label' => 'Choisissez une catégorie'
            ])
            ->add('productCondition', EntityType::class,
            [ 
              'class' => Condition::class,
              'choice_label' => 'state',
              'label' => 'Précisez l\'état du produit'
            ])
            ->add('images', CollectionType::class, [
                'label' => 'Images supplémentaires',
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

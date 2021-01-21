<?php

namespace App\Form;

use App\Entity\Shop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ShopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                "label" => "Nom de ma boutique"
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description de ma boutique",
                'attr' => [
                    'rows' => 7
                ]
            ])
            ->add('adress', TextType::class, [
                "label" => "adresse de ma boutique"
            ])
            ->add('image', FileType::class,[
                "label" => "Ajouter une photo",
                "multiple" => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shop::class,
        ]);
    }
}

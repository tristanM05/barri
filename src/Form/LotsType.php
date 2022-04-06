<?php

namespace App\Form;

use App\Entity\Lots;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, [
                'label' => 'Identifiant',
                'attr' => [
                    'placeholder' => 'Identifiant'
                ]
            ])
            ->add('dateExp', DateType:: class, [
                'label' => 'Date d\'expiration',
                'required' => false,
                "widget" => "single_text",
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                "attr" => [
                    'placeholder' => 'Quantité'
                ]
            ])
            ->add('dateEnter', DateType:: class, [
                'label' => 'Date d\'entré',
                'required' => false,
                "widget" => "single_text",
                "attr" => [
                    'placeholder' => 'date d\'entré'
                ]
            ])
            ->add('place', TextType::class, [
                'label' => 'Lieux de stockage',
                'attr' => [
                    'placeholder' => 'Lieu'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lots::class,
        ]);
    }
}

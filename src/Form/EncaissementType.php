<?php

namespace App\Form;

use App\Entity\Encaissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EncaissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('total', NumberType::class, [
            //     "label" => false,
            //     "required" => false,
            //     "attr" => [
            //         "placeholder" => "Montant (€)"
            //     ]
            // ])
            ->add('comment', TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => [
                    "placeholder" => "Commentaire"
                ]
            ])
            ->add('cb', TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => [
                    "placeholder" => "CB (€)",
                    "class" => "montantEnc"
                ]
            ])
            ->add('esp', TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => [
                    "placeholder" => "ESP (€)",
                    "class" => "montantEnc"
                ]
            ])
            ->add('chq', TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => [
                    "placeholder" => "CHQ (€)",
                    "class" => "montantEnc"
                ]
            ])
            ->add('other', TextType::class, [
                "label" => false,
                "required" => false,
                "attr" => [
                    "placeholder" => "Autre (€)",
                    "class" => "montantEnc"
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Encaissement::class,
        ]);
    }
}

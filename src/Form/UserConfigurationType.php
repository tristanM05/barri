<?php

namespace App\Form;

use App\Entity\UserConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxsupertop', TextType::class, [
                'attr' => [
                    'class' => 'd-inline h2',
                    'style' => 'width:30%'
                ]
            ])
            ->add('maxtop' , NumberType::class, [
                'attr' => [
                    'class' => 'd-inline h2',
                    'style' => 'width:40%'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserConfiguration::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, $this->getConfiguration("Prénom",""))
            ->add('last_name', TextType::class, $this->getConfiguration("Nom",""))
            ->add('email', EmailType::class, $this->getConfiguration("Email",""))
            ->add('pass', PasswordType::class, $this->getConfiguration("Mot de passe", "(6 caractères min)"))
            ->add('passwordVerification', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", ""))
            ->add('adress', TextType::class, $this->getConfiguration("Adresse", ""))
            ->add('postcode', IntegerType::class, $this->getConfiguration("Code Postal", ""))
            ->add('city', TextType::class, $this->getConfiguration("Ville", ""))
            ->add('phone', NumberType::class, $this->getConfiguration("Numéro de téléphone", ""))
            ->add('check', CheckboxType::class,[
                'label' => false,
                'required' => true
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}

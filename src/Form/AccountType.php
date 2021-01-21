<?php

namespace App\Form;

use App\Entity\Client;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, $this->getConfiguration("Prénom","Veuillez entrer votre prénom"))
            ->add('last_name', TextType::class, $this->getConfiguration("Nom","Veuillez entrer votre nom"))
            ->add('email', EmailType::class, $this->getConfiguration("Email","Veuillez entrez votre email"))
            ->add('adress', TextType::class, $this->getConfiguration("Adresse", "Veuillez entrer votre adresse"))
            ->add('postcode', IntegerType::class, $this->getConfiguration("Code Postal", "Veuillez entrer votre CP"))
            ->add('city', TextType::class, $this->getConfiguration("Ville", "Veuillez entrer votre ville"))
            ->add('phone', TextType::class, $this->getConfiguration("Numéro de téléphone", "Veuillez entrez votre numéro de téléphone"))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}

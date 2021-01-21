<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe","Entrez votre mot de passe actuel"))
            ->add("newPassword", PasswordType::class, $this->getConfiguration("Nouveau mot de passe", 
            "Entrez un nouveau mot de passe"))
            ->add("confirmNewPassword", PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Confirmez le nouveau mot de passe"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

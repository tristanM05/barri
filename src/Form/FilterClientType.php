<?php 

namespace App\Form;

use App\Service\FilterClientService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterClientType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('q', TextType::class,[
               'label' => false,
               'required' => false,
               'attr' => [
                   'placeholder' => 'Rechercher'
               ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver){
        
        $resolver->setDefaults([
            'data_class' => FilterClientService::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(){
        return '';
    }
}
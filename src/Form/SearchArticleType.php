<?php 

namespace App\Form;

use App\Service\SearchArticleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchArticleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $client = $options['client'];

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
            'data_class' => SearchArticleService::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'client' => false,
        ]);
    }

    public function getBlockPrefix(){
        return '';
    }
}
<?php 

namespace App\Form;

use App\Entity\Subfamily;
use App\Entity\ProductStatus;
use Doctrine\ORM\EntityRepository;
use App\Service\FilterArticleService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FilterArticleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $client = $options['client'];

        $builder
            ->add('q', TextType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par nom'
                ]
            ])
            ->add('n', TextType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par code-barre'
                ]
            ])
            ->add('status', EntityType::class,[
                'query_builder' => function(EntityRepository $er) use ($client){
                    return $er->createQueryBuilder('i');
                },
                'label' => false,
                'required' => false,
                'choice_attr' => function(?ProductStatus $status){
                    return $status ? ['checked' => 'checked'] : ['checked' => false];
                },
                'class' => ProductStatus::class,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('min',NumberType::class,[
                'label' => false,
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Prix min'
                ]
            ])
            ->add('max',NumberType::class,[
                'label' => false,
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Prix max'
                ]
            ])
            ->add('isvisible',CheckboxType::class,[
                'label' => 'Produits visibles',
                'required' => false
            ])
            // ->add('expired', RadioType::class, [
            //     'label' => 'Article expirés',
            //     'required' => false
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver){
        
        $resolver->setDefaults([
            'data_class' => FilterArticleService::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'client' => false,
        ]);
    }

    public function getBlockPrefix(){
        return '';
    }
}
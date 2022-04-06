<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Fournisseur;
use App\Entity\Salepoint;
use App\Entity\Subfamily;
use App\Entity\ProductStatus;
use Doctrine\ORM\EntityRepository;
use App\Repository\CategoryRepository;
use App\Repository\FournisseurRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $client = $options['client'];

        $builder
            ->add('number', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Numéro d'identification (Ex: 12345678) *"
                ]
            ])
        
            ->add('designation', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Nom du produit *"
                ]
            ])
            ->add('describing', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Description du produit",
                    'rows' => 4
                ]

            ])
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Ajouter une image (optionel)",
                    "class" => false
                ]

            ])

            ->add('costprice', NumberType::class, [
                'label' => "Coût de revient ou prix d'achat (€)",
                'required' => false,
                
            ])
            ->add('referenceprice', NumberType::class, [
                'label' => "Prix de vente de référence (€) *",
            ])
            ->add('specialprice', NumberType::class, [
                'label' => "Prix spécial (€)",
                'attr' => [
                    'style' => 'width: 243px'
                ],
                'required' => false,
            ])
            ->add('productiondate',DateType::class,[
                'label' => false,
                "widget" => "single_text",
                "required" => true
            ])
            ->add('leftdate',DateType::class,[
                'label' => false,
                "widget" => "single_text",
                "required" => false
            ])
            // ->add('lots', CollectionType::class, [
            //     'entry_type' => LotsType::class,
            //     'entry_options' => [
            //         'label' => false
            //     ],
            //     'allow_add' => true,
            //     'by_reference' => false,
            // ])
            // ->add('salepoint', EntityType::class,[
            //     'label' => "Point de vente",
            //     'class' => Salepoint::class,
            //     'query_builder' => function(EntityRepository $er) use ($client){
            //         return $er->createQueryBuilder('i')
            //                   ->where('i.client = :idclient')
            //                   ->andWhere('i.visible = :istrue' )
            //                   ->setParameter('idclient', $client)
            //                   ->setParameter('istrue', 1);
            //     },
            //     'choice_label' => 'getWording',
            // ])
            ->add('productStatus', EntityType::class,[
                'label' => false,
                'class' => ProductStatus::class,
                'choice_label' => 'getWording',
                
            ])
            // ->add('subfamily', EntityType::class,[
            //     'label' => "Catégorie",
            //     'class' => Subfamily::class,
            //     'query_builder' => function(EntityRepository $er) use ($client){
            //         return $er->createQueryBuilder('i')
            //                   ->where('i.client = :idclient')
            //                   ->setParameter('idclient', $client);
            //     },
            //     'choice_label' => 'getWording',
            // ])
            ->add('isvisible', CheckboxType::class,[
                'label' => "Produit visible",
                'required' => false
            ])
            ->add('endDate', DateType::class, [
                'label' => false,
                "widget" => "single_text",
                'required' => false,
            ])
            ->add('dateLimit', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'style' => 'width: 60px'
                ],
                'required' => false,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => "Quantité",
                'required' => false,
                
            ])
            ->add('category', EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'getName',
                'empty_data' => null,
                'placeholder' => '-- Catégorie éxistante --',
                'attr' => [
                    'style' => 'width: 200px',
                ],
                'query_builder' => function(CategoryRepository $c) use ($client)
                                    {
                                        return $c ->createQueryBuilder('c')
                                                    ->where('c.client = :v1')
                                                    ->setParameter('v1', $client)
                                                    ;
                                    }
                
            ])
            ->add('fournisseur', EntityType::class,[
                'label' => false,
                'required' => false,
                'class' => Fournisseur::class,
                'choice_label' => 'getName',
                'empty_data' => null,
                'placeholder' => '-- Origine éxistante --',
                'attr' => [
                    'style' => 'width: 200px'
                ],
                'query_builder' => function(FournisseurRepository $f) use ($client)
                                    {
                                        return $f ->createQueryBuilder('c')
                                                    ->where('c.client = :v1')
                                                    ->setParameter('v1', $client)
                                                    ;
                                    }
                
            ])
            ->add('alert_stock', IntegerType::class,[
                "label" => false,
                'required' => false,
                "attr" => [
                    "placeholder" => "Définir une alerte stock"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class' => Article::class,
            'client' => false,
        ]);
    }
}

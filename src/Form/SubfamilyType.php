<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Family;
use App\Entity\Subfamily;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubfamilyType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client = $options['client'];

        $builder
        
            ->add('wording', TextType::class)
            ->add('family', EntityType::class,[
                'class' => Family::class,
                'query_builder' => function(EntityRepository $er) use ($client){
                    return $er->createQueryBuilder('i')
                              ->where('i.client = :idclient')
                              ->setParameter('idclient', $client);
                },
                'choice_label' => 'getWording',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subfamily::class,
            'client' => false,
        ]);
    }
}

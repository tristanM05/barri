<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Faker\Factory;
use App\Entity\Client;
use App\Entity\Family;
use App\Entity\ProductStatus;
use App\Entity\Salepoint;
use App\Entity\Subfamily;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager){

        $faker = Factory::create('fr-FR');
        $clients = [];
        $salepoints = [];
        $families = [];
        $subfamilies = [];
        $status = [];

        // Create fake users

        for ($i=1; $i < 4; $i++) { 
            
            $client = new Client();
            
            $passwordHash = $this->encoder->encodePassword($client, 'password');

            $client->setEmail($faker->email)
                   ->setPass($passwordHash)
                   ->setAdress($faker->streetAddress)
                   ->setPostcode($faker->postcode)
                   ->setCity($faker->city)
                   ->setPhone($faker->phoneNumber)
                   ->setIsadmin(mt_rand(0, 1));
    
            $manager->persist($client);
            $clients[] = $client;
        }

        // Create fake salepoints
        for ($j=1; $j < 10; $j++) { 
            $salepoint = new Salepoint();

            $client = $clients[mt_rand(0,count($clients) - 1)];

            $salepoint->setWording($faker->word)
                      ->setVisible(mt_rand(0,1))
                      ->setClient($client);

            $manager->persist($salepoint);
            $salepoints[] = $salepoint;
        }

        // Create fake families
        for ($k=1; $k < 5; $k++) { 
            $family = new Family();

            $client = $clients[mt_rand(0,count($clients) - 1)];

            $family->setWording($faker->word)
                   ->setClient($client);

            $manager->persist($family);
            $families[] = $family;
        }

        // Create fake subfamilies
        for ($i=1; $i < 10; $i++) { 
            $subfamily = new Subfamily();

            $family = $families[mt_rand(0,count($families) - 1)];
            $client = $clients[mt_rand(0,count($clients) - 1)];

            $subfamily->setWording($faker->word)
                      ->setFamily($family)
                      ->setClient($client);
            
            $manager->persist($subfamily);
            $subfamilies[] = $subfamily;
        }

        // Create fake setProductStatus
        for ($i=1; $i < 3; $i++) { 
            $status = new ProductStatus();

            $status->setWording($faker->word);

            $manager->persist($status);
            $statuts[] = $status;
        }

        // Create fake articles 
        for ($i=1; $i < 15; $i++) { 
            $article = new Article();

            $client = $clients[mt_rand(0,count($clients) - 1)];
            $salepoint = $salepoints[mt_rand(0,count($salepoints) - 1)];
            $subfamily = $subfamilies[mt_rand(0,count($subfamilies) - 1)];
            $status = $statuts[mt_rand(0,count($statuts) - 1)];

            $article->setDesignation($faker->word)
                    ->setNumber(mt_rand(0000000000000,9999999999999))
                    ->setDescribing($faker->sentence)
                    ->setClient($client)
                    ->setSalepoint($salepoint)
                    ->setProductStatus($status)
                    ->setReferenceprice($faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL))
                    ->setSubfamily($subfamily)
                    ->setIsvisible(mt_rand(0,1));
            
            $manager->persist($article);
        }

        $manager->flush();
    }
}

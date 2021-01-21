<?php

namespace App\Controller\User;

use DateTime;
use App\Form\UserConfigurationType;
use App\Service\DashboardUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserConfigurationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManagementController extends AbstractController{

    /**
     * @Route("/user/management", name="user_management")
     */
    public function indexManagement(DashboardUserService $service, EntityManagerInterface $manager, UserConfigurationRepository $repo, Request $request){

        $user = $this->getUser()->getId();
        $client = $this->getUser();

        // Edit the user last action
        $client->setLast_action(new \DateTime("now"));
        $manager->persist($client);

        // Request for the management
        $allSoldArticles = $service->getAllSoldArticles($user);

        
        // Get the user configuartion
        $user_configuration = $repo->findOneBy(['client' => $client]);

        $superTopConfig = $user_configuration->getMaxsupertop();
        $topConfig = $user_configuration->getMaxtop();

        // Create array & addition of items inside according to its configuration
        $top = [];
        $superTop = [];
        $notTop = [];
        $totalRevenue = 0;
        $countArticle = 0;

        foreach ($allSoldArticles as $article) {

            $date_diff = date_diff($article->getLeftdate(),new DateTime('now'));
            // dump($date_diff->format('%m'));
            // dump($date_diff->format('%Y'));

            // TURNOVER IN THE LAST 12 MONTHS AND CALCULATES THE QUANTITY OF ARTICLE SOLD OVER THE LAST 12 MONTHS
           if ($article->getSpecialPrice() && $date_diff->format('%m') <= 12 && $date_diff->format('%Y') == 00) {
               $totalRevenue += $article->getSpecialPrice();
               $countArticle += 1;
           } else if($date_diff->format('%m') <= 12 && $date_diff->format('%Y') == 00){
               $totalRevenue += $article->getReferencePrice();
               $countArticle += 1;
           } else{}

        //    if ($article->getLeftdate() == null) {
        //        $article->setLeftdate(new DateTime('now')) ;
        //    }
            // CALCULATION OF THE INTERVAL BETWEEN THE PRODUCTION DATE AND THE RELEASE DATE AND THEN PUT IN A COLUMN ACCORDING TO THE RESULT
           $interval = date_diff($article->getLeftdate(),$article->getProductiondate());

           if ($interval->format('%m') <= $superTopConfig && $interval->format('%Y') == 00) {
               $superTop [] = [$article->getDesignation()];
           } elseif($interval->format('%m') > $superTopConfig && $interval->format('%m') <= $topConfig  && $interval->format('%Y') == 00){
               $top [] = [$article->getDesignation()];
           } else {
               $notTop [] = [$article->getDesignation()];
           }
        }

        $form = $this->createForm(UserConfigurationType::class,$user_configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user_configuration);
            $manager->flush();
            // dd();
            $this->addFlash("success","Votre configuration à bien été enregistré");
            return $this->redirectToRoute('user_management');
        }
        

        // Return information to the twig template
        return $this->render('User/management/index_management.html.twig', [
            'totalRevenue' => $totalRevenue,
            'countArticle' => $countArticle,
            'superTop' => $superTop,
            'top' => $top,
            'notTop' => $notTop,
            'topConfig' => $topConfig,
            'superTopConfig' => $superTopConfig,
            'form' => $form->createView()
        ]);
    }

}
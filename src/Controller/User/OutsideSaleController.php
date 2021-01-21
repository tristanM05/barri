<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OutsideSaleController extends AbstractController
{
    /**
     * @Route("/user/outside_sale", name="user_outside_sale")
     */
    public function index(){
        return $this->render('User/outside_sale/outside_sale.html.twig', [
            'controller_name' => 'OutsideSaleController',
        ]);
    }
}

<?php

namespace App\Controller\User;

use App\Repository\FaqRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FaqController extends AbstractController
{
    /**
     * @Route("/faq", name="faq")
     */
    public function index(FaqRepository $faq_repo)
    {
        $faq = $faq_repo->findBy([], ["ordre" => "ASC"]);
        return $this->render('User/faq/index.html.twig', [
            'faq' => $faq,
        ]);
    }
}

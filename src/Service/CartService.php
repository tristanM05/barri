<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{

    protected $session;
    protected $repo;

    public function __construct(SessionInterface $session,ArticleRepository $repo){
        $this->session = $session;
        $this->repo = $repo;
    }


    public function add(int $id){
        
    }

    public function remove(int $id){

    }
}
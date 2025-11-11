<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    #[Route(
        path: '/',
        name: 'app_home2'
    )]
    public function index2(): Response
    {
        return $this->redirectToRoute('app_home');
    }
    #[Route(
        path: [
            'en' => '/en/',
            'fr' => '/fr/',
            'es' => '/es/'
        ],
        name: 'app_home'
    )]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}

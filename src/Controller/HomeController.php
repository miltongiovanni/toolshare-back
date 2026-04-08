<?php

namespace App\Controller;

use App\Service\BreadcrumbService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HomeController extends AbstractController
{
    private $breadcrumbService;
    private $translator;
    public function __construct(BreadcrumbService $breadcrumbService, TranslatorInterface $translator)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->translator = $translator;
    }


    #[Route(
        path: '/',
        name: 'home2'
    )]
    public function index2(): Response
    {
        return $this->redirectToRoute('home');
    }
    #[Route(
        path: [
            'en' => '/en/dashboard',
            'fr' => '/fr/tableau-de-bord',
            'es' => '/es/dashboard'
        ],
        name: 'home'
    )]
    public function index(): Response
    {
        $page_title = $this->translator->trans('menu.dashboard');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $breadcrumbs = $this->breadcrumbService->all();
        return $this->render('home/index.html.twig', [
            'page_title' => $page_title,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }


    #[Route(
        path: '/uploadImage',
        name: 'uploadImage'
    )]
    public function uploadImage(Request $request, FileUploader $fileUploader): Response
    {
        $file = $request->files->get('file');
        $imageFileName = '';
        if ($file) {
            $imageFileName = $fileUploader->upload($file);
        }
        //var_dump($imageFileName);die;
        return $this->json(['location' => $imageFileName]);
    }

}

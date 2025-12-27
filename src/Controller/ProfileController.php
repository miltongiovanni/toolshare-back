<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfilRepository;
use App\Service\BreadcrumbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProfileController extends AbstractController
{
    private $breadcrumbService;
    private $translator;
    public function __construct(BreadcrumbService $breadcrumbService, TranslatorInterface $translator)
    {
        $this->breadcrumbService = $breadcrumbService;
        $this->translator = $translator;
    }

    #[Route(
        path: [
            'en' => '/en/profile/',
            'fr' => '/fr/profil/',
            'es' => '/es/perfil/'
        ],name: 'profile_index', methods: ['GET'])]
    public function index(Request $request, ProfilRepository $profilRepository): Response
    {
        $locale = $request->getLocale();
        $profiles = $profilRepository->getAllProfiles($locale);
        $page_title = $this->translator->trans('menu.profile');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $this->breadcrumbService->add($this->translator->trans('menu.profile'), $this->generateUrl('profile_index'));
        $breadcrumbs = $this->breadcrumbService->all();
        return $this->render('profile/index.html.twig', [
            'page_title' => $page_title,
            'profiles' => $profiles,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    #[Route(
        path: [
            'en' => '/en/profile/new/',
            'fr' => '/fr/profil/noveau/',
            'es' => '/es/perfil/nuevo/'
        ], name: 'profile_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

//    #[Route('/{id}', name: 'profile_show', methods: ['GET'])]
//    public function show(Profile $profile): Response
//    {
//        return $this->render('profile/show.html.twig', [
//            'profile' => $profile,
//        ]);
//    }

    #[Route(
        path: [
            'en' => '/en/profile/{id}/edit/',
            'fr' => '/fr/profil/{id}/editer/',
            'es' => '/es/perfil/{id}/ediction/'
        ], name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profile $profile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    #[Route(
        path: [
            'en' => '/en/profile/{id}/delete',
            'fr' => '/fr/profil/{id}/supprimer',
            'es' => '/es/perfil/{id}/suprimir'
        ], name: 'profile_delete', methods: ['POST'])]
    public function delete(Request $request, Profile $profile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($profile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
    }
}

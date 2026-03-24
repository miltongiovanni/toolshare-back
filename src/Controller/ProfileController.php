<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Service\BreadcrumbService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        $page_title = $this->translator->trans('menu.profile');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $this->breadcrumbService->add($this->translator->trans('menu.profile'), $this->generateUrl('profile_index'));
        $breadcrumbs = $this->breadcrumbService->all();
        return $this->render('profile/index.html.twig', [
            'page_title' => $page_title,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    #[Route('/profile/list', name: 'profile_list', methods: [ 'POST'])]
    public function profile_list(Request $request, ProfileRepository $profileRepository): JsonResponse
    {
        $locale = $request->getLocale();
        $profiles = $profileRepository->getAllProfiles($locale);
        $return = [
            'draw' => 0,
            'recordsTotal' => count($profiles),
            'recordsFiltered' => count($profiles),
            'data' => $profiles
        ];

        return $this->json($return);
    }

    #[Route('/profile/get', name: 'profile_get', methods: ['POST'])]
    public function profile_get(Request $request, ProfileRepository $profileRepository): JsonResponse
    {
        $profile_id = $request->request->get('profile_id');
        $profile = $profileRepository->find($profile_id);
        return $this->json($profile->toArray());
    }

    #[Route('/profile/update', name: 'profile_update', methods: [ 'POST'])]
    public function profile_update(Request $request, ProfileRepository $profileRepository, EntityManagerInterface $em): JsonResponse
    {
        $profile_id = $request->request->get('profile_id');
        $code = $request->request->get('code');
        $description_en = $request->request->get('description_en');
        $description_fr = $request->request->get('description_fr');
        $description_es = $request->request->get('description_es');
        $slug_en = $request->request->get('slug_en');
        $slug_fr = $request->request->get('slug_fr');
        $slug_es = $request->request->get('slug_es');
        $enabled = $request->request->get('enabled', false) === '1';
        if ($profile_id === '0'){
            $profile = new Profile();
            $profile->setCreatedAt(Carbon::now()->toDateTimeImmutable());

        }else{
            $profile = $profileRepository->find($profile_id);
            $profile->setUpdatedAt(Carbon::now()->toDateTimeImmutable());
        }
        $profile->translate('en')->setDescription($description_en);
        $profile->translate('fr')->setDescription($description_fr);
        $profile->translate('es')->setDescription($description_es);
        $profile->translate('en')->setSlug($slug_en);
        $profile->translate('fr')->setSlug($slug_fr);
        $profile->translate('es')->setSlug($slug_es);
        $profile->setCode($code);
        $profile->setEnabled($enabled);
        $profile->mergeNewTranslations();
        $em->persist($profile);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/profile/enable', name: 'profile_enable', methods: [ 'POST'])]
    public function profile_enable(Request $request, ProfileRepository $profileRepository, EntityManagerInterface $em): JsonResponse
    {

        $user_id = $request->request->get('adminUser_id');
        $userAdmin = $profileRepository->find($user_id);
        $userAdmin->setIsActive(true);
        $em->persist($userAdmin);
        $em->flush();
        return $this->json(['success' => true]);
    }
    #[Route('/profile/disable', name: 'profile_disable', methods: [ 'POST'])]
    public function profile_disable(Request $request, ProfileRepository $profileRepository, EntityManagerInterface $em): JsonResponse
    {

        $user_id = $request->request->get('adminUser_id');
        $userAdmin = $profileRepository->find($user_id);
        $userAdmin->setIsActive(false);
        $em->persist($userAdmin);
        $em->flush();
        return $this->json(['success' => true]);
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

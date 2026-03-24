<?php

namespace App\Controller;

use App\Entity\UserAdmin;
use App\Form\UserAdminType;
use App\Repository\ProfileRepository;
use App\Repository\UserAdminRepository;
use App\Service\BreadcrumbService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


final class UserAdminController extends AbstractController
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
            'en' => '/en/admin-user/',
            'fr' => '/fr/usager-admin/',
            'es' => '/es/usuario-admin/'
        ], name: 'user_admin_index', methods: ['GET'])]
    public function index(Request $request, UserAdminRepository $userAdminRepository, ProfileRepository $profileRepository): Response
    {
        $locale = $request->getLocale();
        $page_title = $this->translator->trans('menu.admin.users');
        $this->breadcrumbService->add($this->translator->trans('menu.dashboard'), $this->generateUrl('home'));
        $this->breadcrumbService->add($this->translator->trans('menu.admin.users'), $this->generateUrl('user_admin_index'));
        $breadcrumbs = $this->breadcrumbService->all();
        $profiles = $profileRepository->getAllProfilesActives($locale);
        return $this->render('user_admin/index.html.twig', [
            'profiles' => $profiles,
            'breadcrumbs' => $breadcrumbs,
            'page_title' => $page_title,
        ]);
    }

    #[Route('/user/admin/list', name: 'user_admin_list', methods: [ 'POST'])]
    public function user_admin_list(Request $request, UserAdminRepository $userAdminRepository): JsonResponse
    {
        $locale = $request->getLocale();
        $currentUser = $this->getUser();
        $users = $userAdminRepository->getAdminUsers($locale);
        foreach ($users as &$user) {
            $user['isCurrentAdminUser'] = $currentUser->getId() === $user['id'];
        }
        $return = [
            'draw' => 0,
            'recordsTotal' => count($users),
            'recordsFiltered' => count($users),
            'data' => $users
        ];

        return $this->json($return);
    }

    #[Route('/user/admin/get', name: 'user_admin_get', methods: ['POST'])]
    public function user_admin_get(Request $request, UserAdminRepository $userAdminRepository): JsonResponse
    {
        $user_id = $request->request->get('adminUser_id');
        $userAdmin = $userAdminRepository->find($user_id);
        return $this->json($userAdmin->toArray());
    }

    #[Route('/user/admin/update', name: 'user_admin_update', methods: [ 'POST'])]
    public function user_admin_update(Request $request, UserAdminRepository $userAdminRepository, ProfileRepository $profileRepository, EntityManagerInterface $em,
                                      ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer): JsonResponse
    {
        $user_id = $request->request->get('user_id');
        $profile_id = $request->request->get('profile_id');
        $profile = $profileRepository->find($profile_id);
        $first_name = $request->request->get('first_name');
        $last_name = $request->request->get('last_name');
        $email = $request->request->get('email');
        $is_active = $request->request->get('is_active', false) === '1';
        $locale = $request->request->get('locale');
        if ($user_id === '0'){
            $userAdmin = new UserAdmin();
            $userAdmin->setCreatedAt(Carbon::now()->toDateTimeImmutable());
            $userAdmin->setEmail($email);
            $userAdmin->setSlug(Uuid::v7());
        }else{
            $userAdmin = $userAdminRepository->find($user_id);
        }
        $userAdmin->setFirstName($first_name);
        $userAdmin->setLastName($last_name);
        $userAdmin->setIsActive($is_active);
        $userAdmin->setProfile($profile);
        $userAdmin->setRoles([$profile->getCode()]);
        $userAdmin->setIsVerified(false);
        $em->persist($userAdmin);
        $em->flush();

        if ($user_id === '0'){
            // Generar token seguro
            try {
                $resetToken = $resetPasswordHelper->generateResetToken($userAdmin);

            } catch (ResetPasswordExceptionInterface $e) {
                // If you want to tell the user why a reset email was not sent, uncomment
                // the lines below and change the redirect to 'app_forgot_password_request'.
                // Caution: This may reveal if a user is registered or not.
                //
                // $this->addFlash('reset_password_error', sprintf(
                //     '%s - %s',
                //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
                //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
                // ));

                return $this->redirectToRoute('app_check_email');
            }

            $email = (new TemplatedEmail())
                ->from(new Address('contacto@novaquim.com', 'contacto'))
                ->to(new Address((string) $userAdmin->getEmail(), ($userAdmin->getFirstName().' '.$userAdmin->getLastName())))
                ->subject('Your password reset request')
                ->htmlTemplate('email/verify.email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'name' => $userAdmin->getFirstName().' '.$userAdmin->getLastName(),
                ])
            ;

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd($e->getMessage());
            }

            // Store the token object in session for retrieval in check-email route.
            //$this->setTokenObjectInSession($resetToken);

            $userAdmin->setResetToken($resetToken->getToken());

            $em->persist($userAdmin);
            $em->flush();
        }





        return $this->json(['success' => true]);
    }

    #[Route('/user/admin/enable', name: 'user_admin_enable', methods: [ 'POST'])]
    public function user_admin_enable(Request $request, UserAdminRepository $userAdminRepository, EntityManagerInterface $em): JsonResponse
    {

        $user_id = $request->request->get('adminUser_id');
        $userAdmin = $userAdminRepository->find($user_id);
        $userAdmin->setIsActive(true);
        $em->persist($userAdmin);
        $em->flush();
        return $this->json(['success' => true]);
    }
    #[Route('/user/admin/disable', name: 'user_admin_disable', methods: [ 'POST'])]
    public function user_admin_disable(Request $request, UserAdminRepository $userAdminRepository, EntityManagerInterface $em): JsonResponse
    {

        $user_id = $request->request->get('adminUser_id');
        $userAdmin = $userAdminRepository->find($user_id);
        $userAdmin->setIsActive(false);
        $em->persist($userAdmin);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/{id}', name: 'user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, UserAdmin $userAdmin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userAdmin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userAdmin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}

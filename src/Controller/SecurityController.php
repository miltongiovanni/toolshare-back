<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    #[Route(
        path: [
            'en' => '/en/login',
            'fr' => '/fr/se-connecter',
            'es' => '/es/acceso'
        ],
        name: 'login'
    )]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(
        path: [
            'en' => '/en/change-password',
            'fr' => '/fr/changer-mot-de-passe',
            'es' => '/es/cambiar-clave'
        ],
        name: 'change_password', methods: ['get']
    )]
    public function change_password(): Response
    {

        return $this->render('security/change_password.html.twig', [

        ]);
    }

    #[Route(
        path: [
            'en' => '/en/update-password',
            'fr' => '/fr/modifier-mot-de-passe',
            'es' => '/es/modificar-clave'
        ],
        name: 'update_password', methods: ['post']
    )]
    public function update_password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $newPassword = $request->request->get('new_password');
        $confirmPassword = $request->request->get('confirm_password');
        $oldPassword = $request->request->get('old_password');
        $success = false;
        //dd($request->request->all(), $user->getPassword(), $oldPassword, $passwordHasher->isPasswordValid($user, $oldPassword));
        if ($newPassword === $confirmPassword) {
            if ($newPassword === $oldPassword) {
                $this->addFlash(
                    'error',
                    $translator->trans('auth.password.new.error')
                );
            }else{
                if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                    $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $success = true;
                }else{
                    $this->addFlash(
                        'error',
                        $translator->trans('auth.password.old.error')
                    );
                }
            }
        }else{
            $this->addFlash(
                'error',
                $translator->trans('auth.password.no_match')
            );
        }
        if ($success) {
            return $this->redirectToRoute('logout');
        }
        return $this->redirectToRoute('change_password');

    }

    #[Route(
        path: [
            'en' => '/en/logout',
            'fr' => '/fr/logout',
            'es' => '/es/logout'
        ], name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/connect/google', name: 'connect_google_start')]
    public function google(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('google')
            ->redirect(['email', 'profile']);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function googleCheck() {}

    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function facebook(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('facebook')
            ->redirect(['email']);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function facebookCheck() {}
}

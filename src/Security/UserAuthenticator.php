<?php

namespace App\Security;

use App\Repository\UserAdminRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    private UserAdminRepository $userAdminRepository;
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;
    private EntityManagerInterface $entityManager;
    public function __construct(
        UserAdminRepository $userAdminRepository, EntityManagerInterface $entityManager, Security $security, UrlGeneratorInterface $urlGenerator
    ) {
        $this->userAdminRepository = $userAdminRepository;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = (string) $request->request->get('_username', '');
        $password = (string) $request->request->get('_password', '');
        $user = $this->userAdminRepository->findOneBy(['email' => $username]);

        //to do check if is validated
        return new Passport(
            new UserBadge($username, function (string $userIdentifier): ?UserInterface {

                $user = $this->userAdminRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Email no encontrado.');
                }

                if (!$user->isVerified()) {
                    throw new CustomUserMessageAuthenticationException(
                        'Debes activar tu cuenta desde el correo electrónico.'
                    );
                }

                if (!$user->getPassword()) {
                    throw new CustomUserMessageAuthenticationException(
                        'Debes crear tu contraseña desde el enlace enviado por correo.'
                    );
                }

                return $user;
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        $user = $this->security->getUser();
        $user->setLastLogin(Carbon::now()->toDateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    // public function start(Request $request, ?AuthenticationException $authException = null): Response
    // {
    //     /*
    //      * If you would like this class to control what happens when an anonymous user accesses a
    //      * protected page (e.g. redirect to /login), uncomment this method and make this class
    //      * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //      *
    //      * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //      */
    // }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login');
    }
}

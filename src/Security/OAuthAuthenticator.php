<?php

namespace App\Security;

use App\Entity\UserAdmin;
use App\Repository\UserAdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class OAuthAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private UserAdminRepository $userAdminRepository,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator
    ) {}
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        //return false;
         return in_array($request->attributes->get('_route'), [
                    'connect_google_check',
                    'connect_facebook_check'
                ]);
    }

    public function authenticate(Request $request): Passport
    {
        $route = $request->attributes->get('_route');

        $provider = match ($route) {
            'connect_google_check' => 'google',
            'connect_facebook_check' => 'facebook',
        };

        $client = $this->clientRegistry->getClient($provider);
        $oauthUser = $client->fetchUser();

        $email = $oauthUser->getEmail();
        $oauthId = $oauthUser->getId();

        return new SelfValidatingPassport(
            new UserBadge($email ?? $oauthId, function () use ($email, $oauthId, $provider) {

                // 1. Buscar por email
                $user = $email
                    ? $this->userAdminRepository->findOneBy(['email' => $email])
                    : null;

                // 2. Si no existe → crear
                if (!$user) {
                    $user = new UserAdmin();
                    $user->setEmail($email);
                }

                // 3. Vincular provider
                if ($provider === 'google') {
                    $user->setGoogleId($oauthId);
                }

                if ($provider === 'facebook') {
                    $user->setFacebookId($oauthId);
                }

                $this->em->persist($user);
                $this->em->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new RedirectResponse($this->urlGenerator->generate('login'));
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
}

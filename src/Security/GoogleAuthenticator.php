<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class GoogleAuthenticator implements AuthenticatorInterface
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() == '/register/google/check' && $request->isMethod('GET');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->fetchAccessToken($this->getGoogleClient());
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($googleUser->getEmail());
            $user->setUsername($googleUser->getName());
            $user->setPicture($googleUser->getAvatar());
            $user->setRoles(['ROLE_CLIENT']); // Fix typo here
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new SelfValidatingPassport(new UserBadge($email));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getRoleNames();
        $targetUrl = null;

        if (in_array('ROLE_ADMIN', $roles)) {
            $targetUrl = $this->router->generate('base_admin');
        } elseif (in_array('ROLE_CLIENT', $roles)) {
            $targetUrl = $this->router->generate('base_client');
        } elseif (in_array('ROLE_DESIGNER', $roles)) {
            $targetUrl = $this->router->generate('base_designer');
        }

        return $targetUrl ? new RedirectResponse($targetUrl) : null;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse('/login');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }
    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        return new PostAuthenticationGuardToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }
}

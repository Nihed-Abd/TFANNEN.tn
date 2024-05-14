<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class PreventLoginAccessListener implements EventSubscriberInterface
{
    private $security;
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $rolesToRouteMapping = [
            'ROLE_CLIENT' => 'base_client',
            'ROLE_ADMIN' => 'base_admin',
            'ROLE_DESIGNER' => 'base_designer',
        ];

        foreach ($rolesToRouteMapping as $role => $homeRoute) {
            if ($this->security->isGranted($role) && $request->attributes->get('_route') === 'app_login') {
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate($homeRoute)));
                break; // Stop checking roles after the first match
            }
        }
    }

    

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
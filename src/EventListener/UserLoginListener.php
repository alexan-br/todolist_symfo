<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserLoginListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $securityLogger)
    {
        $this->logger = $securityLogger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        // Vérifiez que l'objet $user est de type User, pour éviter les erreurs en cas de token non lié à un User
        if ($user instanceof \App\Entity\User) {
            $email = $user->getEmail();
            $timestamp = date('Y-m-d H:i:s');

            $this->logger->info("User with email '$email' logged in at $timestamp");
        } else {
            // Loguer un message si le token n'est pas associé à une instance de User
            $this->logger->warning("User login attempt detected, but the user object is not of type User.");
        }
    }
}

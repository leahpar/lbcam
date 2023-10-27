<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AutoLoginSubscriber // implements EventSubscriberInterface
{

    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ){}

    public static function getSubscribedEvents()
    {
        return [
             KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => 'admin']);
        //$this->security->login($user);
    }

}

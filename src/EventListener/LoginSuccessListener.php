<?php

namespace App\EventListener;

use App\Entity\UserAdmin;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessListener
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof UserAdmin) {
            $user->setLastLogin(Carbon::now()->toDateTimeImmutable());
            $this->em->flush();
        }
    }
}

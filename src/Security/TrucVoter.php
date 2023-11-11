<?php

namespace App\Security;

use App\Entity\Truc;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TrucVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }
        if (!$subject instanceof Truc) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            default => throw new \LogicException("'$attribute' is not supported"),
        };
    }

    private function canView(Truc $truc, User $user): bool
    {
        dump($truc, $truc->user->getId(), $user->getId(), $truc->user->getId() == $user->getId());
        return $truc->publie || $truc->user === $user;
    }

    private function canEdit(Truc $truc, User $user): bool
    {
        return $truc->user === $user;
    }

}

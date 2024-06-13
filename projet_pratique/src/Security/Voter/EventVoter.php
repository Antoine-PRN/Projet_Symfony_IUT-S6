<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class EventVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Event $event */
        $event = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($event, $user);
            case self::EDIT:
                return $this->canEdit($event, $user);
            case self::DELETE:
                return $this->canDelete($event, $user);
        }

        return false;
    }

    private function canView(Event $event, User $user): bool
    {
        if ($event->getIsPublic()) {
            return true;
        }

        return $user === $event->getCreator();
    }

    private function canEdit(Event $event, User $user): bool
    {
        return $user === $event->getCreator();
    }

    private function canDelete(Event $event, User $user): bool
    {
        return $user === $event->getCreator();
    }
}

<?php

namespace App\Security;
use App\Entity\Member;
use App\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PostVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Post &&
            in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return self::canView($token->getUser());
            case self::EDIT:
                return self::canEdit($token->getUser());
            case self::DELETE:
                return self::canDelete($token->getUser());
        }

        throw new \LogicException('This code should not be reached!');
    }

    private static function canView(?Member $member): bool
    {
        return self::isMember($member);
    }

    private static function canEdit(?Member$member): bool
    {
        return self::isAdmin($member);
    }

    private static function canDelete(?Member$member): bool
    {
        return self::isAdmin($member);
    }
}
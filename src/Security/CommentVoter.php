<?php

namespace App\Security;
use App\Entity\Comment;
use App\Entity\Member;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommentVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const PUBLISH = 'publish';
    const RATE = 'rate';
    const DELETE = 'delete';


    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Comment &&
            in_array($attribute, [self::VIEW, self::EDIT, self::PUBLISH, self::RATE, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return self::canView($token->getUser());
            case self::EDIT:
                return self::canEdit($subject, $token->getUser());
            case self::PUBLISH:
                return self::canPublish($token->getUser());
            case self::RATE:
                return self::canRate($subject, $token->getUser());
            case self::DELETE:
                return self::canDelete($subject, $token->getUser());
        }

        throw new \LogicException('This code should not be reached!');
    }

    private static function canView(?Member $member): bool
    {
        return self::isMember($member);
    }

    private static function canEdit(Comment $comment, ?Member $member): bool
    {
        return self::isAdmin($member) || self::isAuthor($comment, $member);
    }

    private static function canPublish(?Member $member): bool
    {
        return self::isAdmin($member);
    }

    private static function canRate(Comment $comment, ?Member $member): bool
    {
        return self::isMember($member) && !self::isAuthor($comment, $member);
    }

    private static function canDelete(Comment $comment, ?Member $member): bool
    {
        return self::isAdmin($member) || self::isAuthor($comment, $member);
    }

    private static function isAuthor(Comment $comment, ?Member $member): bool
    {
        return $comment->getAuthor() === $member;
    }
}
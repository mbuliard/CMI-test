<?php

namespace App\Security;

use App\Entity\Member;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{

    protected static function isAdmin(?Member $member): bool
    {
        return $member && $member->isAdmin();
    }

    protected static function isMember(?Member $member): bool
    {
        return (bool) $member;
    }

}
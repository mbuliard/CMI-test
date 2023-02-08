<?php

namespace App\Tests\Helper;

use App\Entity\Member;
use App\Repository\MemberRepository;

/**
 * To be used in class extending PHPUnit\Framework\TestCase
 */
trait MockMemberRepositoryTrait
{
    protected function mockMemberRepository(?Member $memberToReturn = null, string $usernameToFind = 'username'): MemberRepository
    {
        $memberToReturn = $memberToReturn ?? new Member($usernameToFind);
        $repository = $this->createMock(MemberRepository::class);
        $repository
            ->method('findOneBy')
            ->will($this->returnCallback(function ($arg) use ($memberToReturn, $usernameToFind) {
                return ($arg['username'] === $usernameToFind) ? $memberToReturn : null;
            }));

        return $repository;
    }

}
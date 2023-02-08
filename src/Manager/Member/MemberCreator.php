<?php

namespace App\Manager\Member;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberCreator
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly IsUsernameTaken $isUsernameTaken,
    ) {}

    public function __invoke(
        string  $username,
        string $password,
        array $roles = [],
        bool $flush = true
    ): Member {
        $member = new Member($username, $roles);
        $member->setPassword($this->hasher->hashPassword($member, $password));
        $this->check($member);

        $this->entityManager->persist($member);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $member;
    }

    public function createAdmin(string $username, string $password, bool $flush = true): Member
    {
        return $this->__invoke($username, $password, ['ROLE_ADMIN'], $flush);
    }

    private function check(Member $member): void
    {
        if ($this->isUsernameTaken->__invoke($member->getUsername())) {
            throw new \RuntimeException("Username ".$member->getUsername()." is already taken");
        }
    }
}
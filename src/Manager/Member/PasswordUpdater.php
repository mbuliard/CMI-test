<?php

namespace App\Manager\Member;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdater
{
    public function __construct(
        private readonly MemberRepository $repository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(string $username, string $newPassword): Member
    {
        $member = $this->getMember($username);
        $member->setPassword($this->hasher->hashPassword($member, $newPassword));

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    private function getMember(string $username): Member
    {
        $member = $this->repository->findOneBy(['username' => $username]);
        if (is_null($member)) {
            throw new \RuntimeException('User '.$username.' not found.');
        }

        return $member;
    }
}
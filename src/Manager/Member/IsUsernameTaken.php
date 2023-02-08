<?php

namespace App\Manager\Member;

use App\Repository\MemberRepository;

class IsUsernameTaken
{
    public function __construct(
        private readonly MemberRepository $repository,
    ) {}

    public function __invoke(string $username): bool
    {
        return null !== $this->repository->findOneBy(['username' => $username]);
    }
}
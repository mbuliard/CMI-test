<?php

namespace App\Tests\Manager\Member;

use App\Entity\Member;
use App\Manager\Member\PasswordUpdater;
use App\Tests\Helper\MockEntityManagerTrait;
use App\Tests\Helper\MockHasherTrait;
use App\Tests\Helper\MockMemberRepositoryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdaterTest extends TestCase
{
    use MockEntityManagerTrait;
    use MockHasherTrait;
    use MockMemberRepositoryTrait;

    public function testOK(): void
    {
        $member = $this->buildOkPasswordUpdater()('username', 'newPassword');
        $this->assertNotEquals($this->defaultMember()->getPassword(), $member->getPassword());
    }

    public function testMemberNotFound(): void
    {
        $this->expectExceptionObject(new \RuntimeException('User notFound not found.'));
        $member = $this->buildKoPasswordUpdater()('notFound', 'newPassword');

        $this->assertNotEquals($this->defaultMember()->getPassword(), $member->getPassword());
    }

    private function buildOkPasswordUpdater(): PasswordUpdater
    {
        return new PasswordUpdater(
            $this->mockMemberRepository($this->defaultMember()),
            $this->mockHasher(),
            $this->mockOkEntityManager()
        );
    }

    private function buildKoPasswordUpdater(): PasswordUpdater
    {
        return new PasswordUpdater(
            $this->mockMemberRepository($this->defaultMember()),
            $this->createMock(UserPasswordHasherInterface::class),
            $this->mockKoEntityManager()
        );
    }

    private function defaultMember(): Member
    {
        return (new Member('username'))->setPassword('oldHashPassword');
    }
}
<?php

namespace App\Tests\Manager\Member;

use App\Manager\Member\IsUsernameTaken;
use App\Manager\Member\MemberCreator;
use App\Tests\Helper\MockEntityManagerTrait;
use App\Tests\Helper\MockHasherTrait;
use PHPUnit\Framework\TestCase;

class MemberCreatorTest extends TestCase
{
    use MockEntityManagerTrait;
    use MockHasherTrait;

    public function testCreateMember(): void
    {
        $member = $this->buildOkFullManager()('username', 'plainPassword');
        $this->assertEquals('username', $member->getUsername());
        $this->assertEquals(['ROLE_USER'], $member->getRoles());
    }

    public function testCreateMemberWithRole(): void
    {
        $member = $this->buildOkFullManager()('username', 'plainPassword', ['ROLE_TEST']);
        $this->assertEquals('username', $member->getUsername());
        $this->assertEquals(['ROLE_TEST', 'ROLE_USER'], $member->getRoles());
    }

    public function testCreateAdmin(): void
    {
        $member = $this->buildOkFullManager()->createAdmin('username', 'plainPassword');
        $this->assertEquals('username', $member->getUsername());
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $member->getRoles());
    }

    public function testUsernameTaken(): void
    {
        $this->expectExceptionObject(new \RuntimeException('Username takenUsername is already taken'));
        $this->buildKoManager()('takenUsername', 'plainPassword');
    }

    private function buildOkFullManager(): MemberCreator
    {
        return new MemberCreator(
            $this->mockHasher(),
            $this->mockOkEntityManager(),
            $this->mockIsUsernameTaken()
        );
    }

    private function buildKoManager(): MemberCreator
    {
        return new MemberCreator(
            $this->mockHasher(),
            $this->mockKoEntityManager(),
            $this->mockIsUsernameTaken()
        );
    }

    private function mockIsUsernameTaken(): IsUsernameTaken
    {
        $isUsernameTaken = $this->createMock(IsUsernameTaken::class);
        $isUsernameTaken
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->returnCallback(function ($arg) {
                return ($arg === 'takenUsername');
            }));

        return $isUsernameTaken;
    }
}
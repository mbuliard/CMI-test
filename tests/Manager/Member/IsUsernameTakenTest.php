<?php

namespace App\Tests\Manager\Member;

use App\Manager\Member\IsUsernameTaken;
use App\Tests\Helper\MockMemberRepositoryTrait;
use PHPUnit\Framework\TestCase;

class IsUsernameTakenTest extends TestCase
{
    private const TAKEN_USERNAME = 'takenUsername';

    use MockMemberRepositoryTrait;

    public function testTrue(): void
    {
        $this->assertTrue($this->buildService()(self::TAKEN_USERNAME));
    }

    public function testFalse(): void
    {
        $this->assertFalse($this->buildService()('newUsername'));
    }

    private function buildService(): IsUsernameTaken
    {
        return new IsUsernameTaken(
            $this->mockMemberRepository(null, self::TAKEN_USERNAME)
        );
    }
}
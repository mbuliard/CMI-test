<?php

namespace App\Tests\Helper;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * To be used in class extending PHPUnit\Framework\TestCase
 */
trait MockHasherTrait
{

    protected function mockHasher(): UserPasswordHasherInterface
    {
        $hasher = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->addMethods(['hashPassword'])
            ->getMock();
        $hasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn('encodedPassword');

        return $hasher;
    }
}
<?php

namespace App\Tests\Helper;

use Doctrine\ORM\EntityManagerInterface;

/**
 * To be used in class extending PHPUnit\Framework\TestCase
 */
trait MockEntityManagerTrait
{
    protected function mockOkEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        return $entityManager;
    }

    protected function mockKoEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('persist');
        $entityManager->expects($this->never())->method('flush');

        return $entityManager;
    }

}
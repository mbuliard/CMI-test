<?php

namespace App\Entity\Timestampable;

interface TimestampableInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;
    public function getUpdatedAt(): ?\DateTimeImmutable;
}
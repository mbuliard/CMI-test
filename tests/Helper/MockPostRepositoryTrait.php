<?php

namespace App\Tests\Helper;

use App\Entity\Post;
use App\Repository\PostRepository;

/**
 * To be used in class extending PHPUnit\Framework\TestCase
 */
trait MockPostRepositoryTrait
{
    protected function mockPostRepository(?Post $postToReturn = null): PostRepository
    {
        $postToReturn = $postToReturn ?? new Post('title', 'body');
        $repository = $this->createMock(PostRepository::class);
        $repository
            ->method('find')
            ->will($this->returnCallback(function ($arg) use ($postToReturn) {
                return ($arg === 'postId') ? $postToReturn : null;
            }));

        return $repository;
    }

}
<?php

namespace App\Tests\Helper;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;

/**
 * To be used in class extending PHPUnit\Framework\TestCase
 */
trait MockCommentRepositoryTrait
{
    protected function mockCommentRepository(?Comment $commentToReturn = null): CommentRepository
    {
        $commentToReturn = $commentToReturn ?? new Comment('body');
        $commentToReturn->setParent(new Post('title', 'body'));
        $repository = $this->createMock(CommentRepository::class);
        $repository
            ->method('find')
            ->will($this->returnCallback(function ($arg) use ($commentToReturn) {
                return ($arg === 'commentId') ? $commentToReturn : null;
            }));

        return $repository;
    }

}
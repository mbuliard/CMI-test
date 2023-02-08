<?php

namespace App\Tests\Manager;

use App\Entity\Comment;
use App\Entity\CommentRating;
use App\Entity\Member;
use App\Entity\Post;
use App\Manager\CommentRater;
use App\Tests\Helper\MockCommentRepositoryTrait;
use App\Tests\Helper\MockEntityManagerTrait;
use PHPUnit\Framework\TestCase;

class CommentRaterTest extends TestCase
{
    use MockEntityManagerTrait;
    use MockCommentRepositoryTrait;

    public function testRate(): void
    {
        $member = new Member('username');
        $comment = self::getComment();
        $commentRating = (new CommentRater(
            $this->mockOkEntityManager(),
        ))($comment, $member, 4);
        $this->assertEquals($member, $commentRating->getAuthor());
        $this->assertEquals($comment, $commentRating->getComment());
        $this->assertEquals(4, $commentRating->getRating());
    }

    public function testIsAuthor(): void
    {
        $member = new Member('username');
        $this->expectExceptionObject(
            new \RuntimeException(
                'App\Manager\CommentRater::check : Comment cannot be rated by its author'
            )
        );
        (new CommentRater(
            $this->mockKoEntityManager(),
        ))(self::getComment($member), $member, 4);
    }

    public function testAlreadyRated(): void
    {
        $member = new Member('username');
        $comment = self::getComment();
        $previousRating = new CommentRating($comment, $member, 0);
        $comment->addRating($previousRating);
        $this->expectExceptionObject(
            new \RuntimeException(
                'App\Manager\CommentRater::check : Comment can only be rated once by member'
            )
        );
        (new CommentRater(
            $this->mockKoEntityManager(),
        ))($comment, $member, 4);
    }

    private static function getComment(?Member $author = null): Comment
    {
        $comment = new Comment('commentBody');
        $comment->setParent(new Post('postTitle', 'postBody'));
        $comment->setAuthor($author);

        return $comment;
    }
}
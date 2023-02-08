<?php

namespace App\Tests\Security;

use App\Entity\Comment;
use App\Entity\Member;
use App\Entity\Post;
use App\Security\CommentVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoterTest extends TestCase
{
    public function testAnonymous(): void
    {
        $this->assertEquals(
            CommentVoter::ACCESS_DENIED,
            (new CommentVoter())->vote(
                new NullToken(),
                self::getComment(),
                [CommentVoter::VIEW, CommentVoter::EDIT, CommentVoter::PUBLISH, CommentVoter::RATE, CommentVoter::DELETE]
            )
        );
    }

    public function testMember(): void
    {
        $voter = new CommentVoter();
        $token = self::mockToken();
        $comment = self::getComment();

        $this->assertEquals(
            CommentVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::VIEW]
            )
        );

        $this->assertEquals(
            CommentVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::RATE]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_DENIED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::EDIT, CommentVoter::PUBLISH, CommentVoter::DELETE]
            )
        );
    }

    public function testAuthor(): void
    {
        $voter = new CommentVoter();
        $author = self::getMember();
        $token = self::mockToken($author);
        $comment = self::getComment($author);

        $this->assertEquals(
            CommentVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::VIEW]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::EDIT]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::DELETE]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_DENIED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::PUBLISH, CommentVoter::RATE]
            )
        );
    }

    public function testAdmin(): void
    {
        $voter = new CommentVoter();
        $token = self::mockToken(self::getAdmin());
        $comment = self::getComment();

        $this->assertEquals(
            CommentVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::VIEW]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::EDIT]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::PUBLISH]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $comment,
                [CommentVoter::DELETE]
            )
        );
    }

    private static function getComment(?Member $author = null): Comment
    {
        $author = $author ?? self::getMember();
        $comment = new Comment('body');
        $comment->setParent(new Post('title', 'body'));
        $comment->setAuthor($author);

        return $comment;
    }

    private static function mockToken(?Member $member = null): TokenInterface
    {
        $member = $member ?? self::getMember();
        return new UsernamePasswordToken($member, 'firewall');
    }

    private static function getAdmin(): Member
    {
        return new Member('username', ['ROLE_ADMIN']);
    }

    private static function getMember(): Member
    {
        return new Member('username');
    }
}
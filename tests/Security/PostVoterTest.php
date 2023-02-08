<?php

namespace App\Tests\Security;

use App\Entity\Member;
use App\Entity\Post;
use App\Security\PostVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoterTest extends TestCase
{
    public function testAnonymous(): void
    {
        $this->assertEquals(
            PostVoter::ACCESS_DENIED,
            (new PostVoter())->vote(
                new NullToken(),
                self::getPost(),
                [PostVoter::VIEW, PostVoter::EDIT, PostVoter::DELETE]
            )
        );
    }

    public function testMember(): void
    {
        $voter = new PostVoter();
        $token = self::mockToken();
        $post = self::getPost();

        $this->assertEquals(
            PostVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $post,
                [PostVoter::VIEW]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_DENIED,
            $voter->vote(
                $token,
                $post,
                [PostVoter::EDIT, PostVoter::DELETE]
            )
        );
    }

    public function testAdmin(): void
    {
        $voter = new PostVoter();
        $token = self::mockToken(self::getAdmin());
        $post = self::getPost();

        $this->assertEquals(
            PostVoter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $post,
                [PostVoter::VIEW]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $post,
                [PostVoter::EDIT]
            )
        );

        $this->assertEquals(
            Voter::ACCESS_GRANTED,
            $voter->vote(
                $token,
                $post,
                [PostVoter::DELETE]
            )
        );
    }

    private static function getPost(?Member $author = null): Post
    {
        $author = $author ?? self::getMember();
        $post = new Post('title', 'body');
        $post->setAuthor($author ?? self::getMember());

        return $post;
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
<?php

namespace App\Manager;

use App\Entity\Comment;
use App\Entity\CommentRating;
use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;

class CommentRater
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function __invoke(Comment $comment, Member $member, int $rating, bool $flush = true): CommentRating
    {
        $this->check($comment, $member);
        $commentRating = new CommentRating($comment, $member, $rating);
        $comment->addRating($commentRating);
        $member->addCommentRating($commentRating);

        $this->entityManager->persist($commentRating);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $commentRating;
    }

    private function check(Comment $comment, Member $member): void
    {
        if (self::isAuthor($comment, $member)) {
            throw new \RuntimeException(__METHOD__.' : Comment cannot be rated by its author');
        }
        if ($this->hasRated($comment, $member)) {
            throw new \RuntimeException(__METHOD__.' : Comment can only be rated once by member');
        }
    }

    private function hasRated(Comment $comment, Member $member): bool
    {
        foreach ($comment->getRatings() as $rating) {
            if ($rating->getAuthor() === $member) {
                return true;
            }
        }

        return false;
    }

    private static function isAuthor(Comment $comment, Member $member): bool
    {
        return $comment->getAuthor() === $member;
    }
}
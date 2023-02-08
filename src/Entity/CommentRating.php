<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Authorable\AuthorableInterface;
use App\Entity\Authorable\AuthorableTrait;
use App\Repository\CommentRatingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRatingRepository::class)]
#[UniqueEntity(
    fields: ['comment', 'author'],
    message: 'Member cannot rate the same comment twice',
    errorPath: 'comment'
)]
class CommentRating implements AuthorableInterface
{
    public const MAX = 5;
    public const MIN = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comment $comment;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Range(
        notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}',
        min: self::MIN,
        max: self::MAX,
    )]
    private ?int $rating;

    #[ORM\ManyToOne(inversedBy: 'commentRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Member $author;

    use AuthorableTrait;

    public function __construct(Comment $comment, Member $author, int $rating)
    {
        $this->comment = $comment;
        $this->author = $author;
        $this->rating = $rating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}

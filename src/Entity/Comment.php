<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\Controller\Comment\CommentCreatorController;
use App\Controller\Comment\CommentDeleteController;
use App\Controller\Comment\CommentPublicationController;
use App\Controller\Comment\CommentRaterController;
use App\Controller\Comment\CommentUpdateController;
use App\Entity\Authorable\AuthorableInterface;
use App\Entity\Authorable\AuthorableTrait;
use App\Entity\Commentable\Commentable;
use App\Entity\Commentable\CommentableTrait;
use App\Entity\Timestampable\TimestampableInterface;
use App\Entity\Timestampable\TimestampableTrait;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            uriTemplate: '/posts/{postId}/comments',
            uriVariables: [
                'postId' => new Link(toProperty: 'parentPost', fromClass: Post::class),
            ],
            description: 'Retrieves collection of comments linked to a post.',
        ),
        new GetCollection(
            uriTemplate: '/comments/{commentId}/comments',
            uriVariables: [
                'commentId' => new Link(toProperty: 'parentComment', fromClass: Comment::class),
            ],
            description: 'Retrieves collection of comments linked to a comment.',
        ),
        new \ApiPlatform\Metadata\Post(
            status: 202,
            controller: CommentCreatorController::class,
            output: false,
            write: false
        ),
        new Patch(
            controller: CommentUpdateController::class,
            security: "object.getAuthor() == user",
            write: false
        ),
        new Delete(
            controller: CommentDeleteController::class,
            security: "is_granted('ROLE_ADMIN') or object.getAuthor() == user",
            write: false
        ),
        new \ApiPlatform\Metadata\Post(
            uriTemplate: '/comments/{id}/publication',
            controller: CommentPublicationController::class,
            description: 'Publish a comment',
            security: "is_granted('ROLE_ADMIN')",
            name: 'publication',
        ),
        new \ApiPlatform\Metadata\Post(
            uriTemplate: '/comments/{id}/rating',
            controller: CommentRaterController::class,
            description: 'Rate a comment',
            name: 'rating',
        )
    ],
    normalizationContext: ['groups' => ['comment']],
    denormalizationContext: ['groups' => ['write']],
)]
class Comment implements Commentable, TimestampableInterface, AuthorableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[Groups(['post', 'comment'])]
    protected ?string $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post', 'comment', 'write'])]
    protected string $body;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    protected ?Post $parentPost = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    protected ?self $parentComment = null;

    #[ORM\OneToMany(mappedBy: 'parentComment', targetEntity: self::class)]
    #[Groups(['comment'])]
    protected Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    protected ?Member $author;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: CommentRating::class, orphanRemoval: true)]
    protected Collection $ratings;

    #[ORM\Column]
    #[Groups(['comment'])]
    protected bool $isPublished = false;

    use TimestampableTrait;
    use CommentableTrait;
    use AuthorableTrait;

    public function __construct(string $body)
    {
        $this->body = $body;
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getParent(): Commentable
    {
        return $this->parentPost ?? $this->parentComment;
    }

    public function getParentPost(): ?Post
    {
        return $this->parentPost;
    }

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    #[Groups(['comment'])]
    public function getPost(): Post
    {
        return $this->getParentPost() ?? $this->getParentComment()->getPost();
    }

    public function setParent(Commentable $commentable): self
    {
        if ($commentable instanceof Post) {
            $this->parentPost = $commentable;
        } elseif ($commentable instanceof Comment) {
            $this->parentComment = $commentable;
        } else {
            throw new \RuntimeException(__METHOD__.' : '.get_class($commentable).' not allowed.');
        }

        return $this;
    }

    public function getAuthor(): ?Member
    {
        return $this->author;
    }

    public function setAuthor(?Member $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(CommentRating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setComment($this);
        }

        return $this;
    }

    public function removeRating(CommentRating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getComment() === $this) {
                $rating->setComment(null);
            }
        }

        return $this;
    }

    #[Groups(['comment'])]
    public function getRating(): ?float
    {
        if (0 === $this->ratings->count()) {
            return null;
        }

        $sum = 0;
        foreach ($this->getRatings() as $rating) {
            $sum += $rating->getRating();
        }

        return round(
            $sum / $this->ratings->count(),
            1
        );
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function publish(): self
    {
        return $this->setIsPublished(true);
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}

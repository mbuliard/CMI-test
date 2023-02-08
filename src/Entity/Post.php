<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\Controller\Post\PostCreatorController;
use App\Entity\Authorable\AuthorableInterface;
use App\Entity\Authorable\AuthorableTrait;
use App\Entity\Commentable\Commentable;
use App\Entity\Commentable\CommentableTrait;
use App\Entity\Timestampable\TimestampableInterface;
use App\Entity\Timestampable\TimestampableTrait;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['postCollection']]
        ),
        new \ApiPlatform\Metadata\Post(
            controller: PostCreatorController::class,
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['post']],
    denormalizationContext: ['groups' => ['write']],
)]
class Post implements Commentable, TimestampableInterface, AuthorableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[Groups(['post', 'postCollection'])]
    protected ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post', 'postCollection', 'write'])]
    protected string $title;

    #[ORM\Column(length: 255, unique: true)]
    #[Gedmo\Slug(fields: ['title'])]
    #[Groups(['post', 'postCollection'])]
    protected ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post', 'postCollection', 'write'])]
    protected string $body;

    #[ORM\OneToMany(mappedBy: 'parentPost', targetEntity: Comment::class)]
    #[Groups(['post'])]
    #[Link(toProperty: 'company')]
    protected Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Member $author;

    use TimestampableTrait;
    use CommentableTrait;
    use AuthorableTrait;

    public function __construct(
        string $title,
        string $body
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->comments = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
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
}

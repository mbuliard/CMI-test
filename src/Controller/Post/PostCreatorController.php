<?php

namespace App\Controller\Post;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Uid\Uuid;

class PostCreatorController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,

    ) {}

    public function __invoke(Post $post)
    {
        $this->sanitize($post);
        $post->setAuthor($this->getUser());
        $post->setId(Uuid::v1());
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    private function sanitize(Post $post): Post
    {
        $post->setTitle(strip_tags($post->getTitle()));
        $post->setBody(htmlentities($post->getBody()));

        return $post;
    }
}
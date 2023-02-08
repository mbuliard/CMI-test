<?php

namespace App\Controller\Comment;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentPublicationController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Comment $comment)
    {
        $comment->publish();

        $this->entityManager->flush();

        return $comment;
    }

}
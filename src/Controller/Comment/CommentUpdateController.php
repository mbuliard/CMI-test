<?php


namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Message\CommentUpdateMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentUpdateController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ){}

    public function __invoke(Comment $comment): Comment
    {
        $this->bus->dispatch(new CommentUpdateMessage(
            body: $comment->getBody(),
            commentId: $comment->getId()
        ));

        return $comment;
    }

}
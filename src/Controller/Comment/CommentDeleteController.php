<?php


namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Message\CommentDeleteMessage;
use App\Message\CommentUpdateMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentDeleteController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ){}

    public function __invoke($id): string
    {
        $this->bus->dispatch(new CommentDeleteMessage(commentId: $id));

        return $id;
    }

}
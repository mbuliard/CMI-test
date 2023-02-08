<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Message\CommentRatingMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentRaterController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {}

    public function __invoke(Comment $comment, Request $request): Comment
    {
        $this->bus->dispatch(new CommentRatingMessage(
            value: $this->getRating($request),
            commentId: $comment->getId(),
            authorId: $this->getUser()->getId()
        ));

        return $comment;
    }

    private function getRating(Request $request): string
    {
        $data = json_decode($request->getContent());
        if (!isset($data->value)) {
            throw new \RuntimeException('Missing `value` field to create rating.');
        }

        return $data->value;
    }
}
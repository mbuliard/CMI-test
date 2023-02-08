<?php

namespace App\Controller\Comment;

use App\Entity\Comment;
use App\Entity\Commentable\Commentable;
use App\Message\CommentCreationMessage;
use App\Repository\CommentableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentCreatorController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly CommentableRepository $commentableRepository
    ) {}

    public function __invoke(Comment $comment, Request $request): Comment
    {
        $this->bus->dispatch(new CommentCreationMessage(
            body: $comment->getBody(),
            parentId: $this->getParent($request)->getId(),
            authorId: $this->getUser()->getId()
        ));

        return $comment;
    }

    private function getParent(Request $request): Commentable
    {
        $commentable = $this->commentableRepository->find($this->getParentId($request));
        if (is_null($commentable)) {
            throw new \RuntimeException('No commentable matching the id');
        }

        return $commentable;
    }

    private function getParentId(Request $request): string
    {
        $data = json_decode($request->getContent());
        if (!isset($data->parent)) {
            throw new \RuntimeException('Missing `parent` to create comment');
        }

        return $data->parent;
    }
}
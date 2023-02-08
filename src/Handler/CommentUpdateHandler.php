<?php


namespace App\Handler;

use App\Entity\Comment;
use App\Message\CommentCreationMessage;
use App\Message\CommentUpdateMessage;
use App\Repository\CommentableRepository;
use App\Repository\CommentRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\Uuid;

class CommentUpdateHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommentRepository $repository
    ) {}

    public function __invoke(CommentUpdateMessage $message): void
    {
        $comment = $this->repository->find(id: $message->commentId);
        $comment->setBody($message->body);

        $this->entityManager->flush();
    }

}
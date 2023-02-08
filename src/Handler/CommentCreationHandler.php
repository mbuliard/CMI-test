<?php


namespace App\Handler;

use App\Entity\Comment;
use App\Message\CommentCreationMessage;
use App\Repository\CommentableRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\Uuid;

class CommentCreationHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommentableRepository $commentableRepository,
        private readonly MemberRepository $memberRepository
    ) {}

    public function __invoke(CommentCreationMessage $message): void
    {
        $comment = new Comment(strip_tags($message->body));
        $comment->setParent(
            $this->commentableRepository->find($message->parentId)
        );
        $comment->setAuthor($this->memberRepository->find($message->authorId));
        $comment->setId(Uuid::v1());

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

}
<?php


namespace App\Handler;

use App\Manager\CommentRater;
use App\Message\CommentRatingMessage;
use App\Repository\CommentRepository;
use App\Repository\MemberRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommentRatingHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly CommentRater $commentRater,
        private readonly CommentRepository $commentRepository,
        private readonly MemberRepository $memberRepository
    ) {}

    public function __invoke(CommentRatingMessage $message): void
    {
        $this->commentRater->__invoke(
            $this->commentRepository->find($message->commentId),
            $this->memberRepository->find($message->authorId),
            $message->value,
        );
    }
}
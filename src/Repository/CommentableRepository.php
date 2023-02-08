<?php

namespace App\Repository;

use App\Entity\Commentable\Commentable;

class CommentableRepository
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository
    ){}

    public function find(string $id): ?Commentable
    {
        return $this->postRepository->find($id) ?? $this->commentRepository->find($id);
    }

}
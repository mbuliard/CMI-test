<?php

namespace App\Entity\Commentable;

use App\Entity\Comment;
use Doctrine\Common\Collections\Collection;

interface Commentable
{
    public function getId(): ?string;
    public function getComments(): Collection;
    public function addComment(Comment $comment): self;
}
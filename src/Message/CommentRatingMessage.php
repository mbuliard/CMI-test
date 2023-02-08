<?php

namespace App\Message;

class CommentRatingMessage
{
    public function __construct(
        public int $value,
        public string $commentId,
        public string $authorId
    ) {
    }
}
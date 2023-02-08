<?php

namespace App\Message;

class CommentCreationMessage
{
    public function __construct(
        public string $body,
        public string $parentId,
        public string $authorId
    ) {
    }
}
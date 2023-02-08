<?php

namespace App\Message;

class CommentUpdateMessage
{
    public function __construct(
        public string $body,
        public string $commentId,
    ) {}
}
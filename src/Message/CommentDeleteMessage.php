<?php

namespace App\Message;

class CommentDeleteMessage
{
    public function __construct(public string $commentId,) {}
}
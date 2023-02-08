<?php

namespace App\Entity\Authorable;

use App\Entity\Member;

interface AuthorableInterface
{
    public function getAuthor(): ?Member;
    public function setAuthor(?Member $author): self;
}
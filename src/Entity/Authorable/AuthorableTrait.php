<?php

namespace App\Entity\Authorable;

use App\Entity\Member;

trait AuthorableTrait
{
    public function getAuthor(): ?Member
    {
        return $this->author;
    }

    public function setAuthor(?Member $author): self
    {
        $this->author = $author;

        return $this;
    }

}
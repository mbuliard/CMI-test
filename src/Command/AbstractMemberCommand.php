<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

abstract class AbstractMemberCommand extends Command
{
    protected const NAME_PREFIX = 'app:member:';
    public const ARG_USERNAME = 'username';
    public const ARG_PASSWORD = 'password';


    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARG_USERNAME,
                InputArgument::REQUIRED,
                'Username of the new member'
            )
            ->addArgument(
                self::ARG_PASSWORD,
                InputArgument::REQUIRED,
                'plain password of the new member'
            )
        ;
    }
}
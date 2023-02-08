<?php

namespace App\Command;

use App\Manager\Member\PasswordUpdater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::NAME,
    description: 'Update the password of a member',
)]
class MemberUpdatePasswordCommand extends AbstractMemberCommand
{
    public const NAME = self::NAME_PREFIX.'update-password';
    public function __construct(
        private readonly PasswordUpdater  $passwordUpdater
    ) {
        parent::__construct(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $member = $this->passwordUpdater->__invoke(
                $input->getArgument(self::ARG_USERNAME),
                $input->getArgument(self::ARG_PASSWORD)
            );
        } catch (\RuntimeException $exception) {
            $io->error($exception->getMessage());

            return Command::INVALID;
        }

        $io->success('Password of '.$member->getUsername().' updated');

        return Command::SUCCESS;
    }
}

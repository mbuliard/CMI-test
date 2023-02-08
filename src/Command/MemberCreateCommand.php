<?php

namespace App\Command;

use App\Manager\Member\MemberCreator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: self::NAME,
    description: 'Create a new member',
)]
class MemberCreateCommand extends AbstractMemberCommand
{
    public const NAME = self::NAME_PREFIX.'create';
    public const OPT_ADMIN = 'admin';
    public function __construct(private readonly MemberCreator $memberManager)
    {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addOption(
            self::OPT_ADMIN,
            null,
            InputOption::VALUE_NONE,
            'With the role admin'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument(self::ARG_USERNAME);
        $password = $input->getArgument(self::ARG_PASSWORD);
        $isAdmin = $input->getOption(self::OPT_ADMIN);

        try {
            $member = $isAdmin ?
                $this->memberManager->createAdmin($username, $password) :
                $this->memberManager->__invoke($username, $password);
        } catch (\RuntimeException $exception) {
            $io->error($exception->getMessage());

            return Command::INVALID;
        }

        $io->success( "User ".$member->getUsername()." created with ".($isAdmin ? "admin" : "basic")." rights !");
        $io->success('Its id is '.$member->getId());

        return Command::SUCCESS;
    }
}

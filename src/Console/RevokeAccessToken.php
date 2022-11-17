<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Communicator\Api;
use Shimoning\LineNotify\Exceptions\MissingAccessTokenException;

final class RevokeAccessToken extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('token:revoke');
        $this->setDescription('Revoke a token');

        $this->addOption('access-token', 't', InputOption::VALUE_REQUIRED, 'Set AccessToken.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Revoke Start</info>');

        $accessToken = $input->getOption('access-token');
        if (empty($accessToken)) {
            throw new MissingAccessTokenException();
        }

        // request
        $result = Api::revokeAccessToken($accessToken);
        if ($result) {
            $output->writeln('<question> - Succeed !</question>');
        } else {
            $output->writeln('<comment> - Failed...</comment>');
        }

        $output->writeln('<info>Revoke End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

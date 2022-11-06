<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Api;
use Shimoning\LineNotify\Exceptions\AccessTokenMissingException;

final class Notify extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('notify');
        $this->setDescription('Send message to LINE.');

        $this->addOption('access-token', 't', InputOption::VALUE_OPTIONAL, 'Set AccessToken, if not input using env.', null);
        $this->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Message to notify.', 'Hello World!');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Notify Start</info>');

        $accessToken = $input->getOption('access-token') ?? $_ENV['ACCESS_TOKEN'];
        if (empty($accessToken)) {
            throw new AccessTokenMissingException();
        }

        $result = Api::notify(
            $accessToken,
            $input->getOption('message') ?? 'TEST.',
        );
        if ($result) {
            $output->writeln('<question> - Succeed !</question>');
        } else {
            $output->writeln('<comment> - Failed...</comment>');
        }

        $output->writeln('<info>Notify End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

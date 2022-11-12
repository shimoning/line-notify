<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Api;
use Shimoning\LineNotify\Exceptions\MissingAccessTokenException;

final class Status extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('status');
        $this->setDescription('Get connection status.');

        $this->addOption('access-token', 't', InputOption::VALUE_OPTIONAL, 'Set AccessToken, if not input using env.', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Status Start</info>');

        $accessToken = $input->getOption('access-token') ?? $_ENV['ACCESS_TOKEN'];
        if (empty($accessToken)) {
            throw new MissingAccessTokenException();
        }

        // request
        $status = Api::status($accessToken);
        if ($status) {
            $output->writeln('<question> - Succeed !</question>');
            $output->writeln('   * target_type: ' . $status->getTargetTypeValue());
            $output->writeln('   * target: ' . $status->getTarget());
        } else {
            $output->writeln('<comment> - Failed...</comment>');
        }

        $output->writeln('<info>Status End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

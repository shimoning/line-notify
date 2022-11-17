<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Communicator\Auth;
use Shimoning\LineNotify\Entity\Output\AuthResult;
use Shimoning\LineNotify\Exceptions\ValidationException;

final class ParseAuthResult extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('auth:parse:result');
        $this->setDescription('Parse Auth Result.');

        $this->addOption('query-string', 's', InputOption::VALUE_REQUIRED, 'Input query string', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Parse Auth Result Start</info>');

        $queryString = $input->getOption('query-string');
        if (empty($queryString)) {
            throw new ValidationException('query-string は必須です。');
        }

        // parse
        $result = Auth::parseAuthResult($queryString);
        if ($result instanceof AuthResult) {
            $output->writeln('<question> - Succeed !</question>');
            $output->writeln('   * code: ' . $result->getCode());
            $output->writeln('   * state: ' . $result->getState());
        } else {
            $output->writeln('<comment> - Failed...</comment>');
            $output->writeln('   * error: ' . $result->getError());
            $output->writeln('   * description: ' . $result->getErrorDescription());
        }

        $output->writeln('<info>Parse Auth Result End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

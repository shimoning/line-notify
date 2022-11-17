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

        $this->addOption('query-string', 's', InputOption::VALUE_REQUIRED, 'Input query string or JSON', null);
        $this->addOption('query-keys', 'k', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Input keys of query array', []);
        $this->addOption('query-values', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Input values of query array', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Parse Auth Result Start</info>');

        $query = null;
        if ($input->getOption('query-string')) {
            $query = $input->getOption('query-string');
        } else {
            $keys = $input->getOption('query-keys');
            $values = $input->getOption('query-values');
            if (\count($keys) !== \count($values)) {
                throw new ValidationException('query-keys と query-values は同じ数にしてください。');
            }
            $query = \array_combine($keys, $values);
        }
        if (empty($query)) {
            throw new ValidationException('query-stringは必須です。');
        }

        // parse
        $result = Auth::parseAuthResult($query);
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

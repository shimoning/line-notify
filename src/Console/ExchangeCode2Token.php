<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Communicator\Auth;
use Shimoning\LineNotify\Exceptions\MissingClientIdException;
use Shimoning\LineNotify\Exceptions\MissingClientSecretException;
use Shimoning\LineNotify\Exceptions\MissingRedirectUriException;
use Shimoning\LineNotify\Exceptions\ValidationException;

final class ExchangeCode2Token extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('auth:exchange:token');
        $this->setDescription('Exchange code to token.');

        $this->addOption('client-id', 'i', InputOption::VALUE_OPTIONAL, 'Set ClientId. If not input, using env.', null);
        $this->addOption('client-secret', 's', InputOption::VALUE_OPTIONAL, 'Set ClientSecret. If not input, using env.', null);
        $this->addOption('redirect-uri', 'u', InputOption::VALUE_OPTIONAL, 'Set RedirectUri. If not input, using env.', null);
        $this->addOption('code', 'c', InputOption::VALUE_REQUIRED, 'Set Code included in callback query.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Exchange code to token Start</info>');

        if (empty($input->getOption('code'))) {
            throw new ValidationException('code は必須です。');
        }

        $clientId = $input->getOption('client-id') ?? $_ENV['CLIENT_ID'];
        if (empty($clientId)) {
            throw new MissingClientIdException();
        }

        $clientSecret = $input->getOption('client-secret') ?? $_ENV['CLIENT_SECRET'];
        if (empty($clientSecret)) {
            throw new MissingClientSecretException();
        }

        $redirectUri = $input->getOption('redirect-uri') ?? $_ENV['REDIRECT_URI'];
        if (empty($redirectUri)) {
            throw new MissingRedirectUriException();
        }

        // parse
        $result = Auth::token(
            $clientId,
            $clientSecret,
            $redirectUri,
            $input->getOption('code'),
        );
        if ($result) {
            $output->writeln('<question> - Succeed !</question>');
            $output->writeln('   * token: ' . $result);
        } else {
            $output->writeln('<comment> - Failed...</comment>');
        }

        $output->writeln('<info>Exchange code to token End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

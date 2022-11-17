<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Communicator\Auth;
use Shimoning\LineNotify\Exceptions\MissingClientIdException;
use Shimoning\LineNotify\Exceptions\MissingRedirectUriException;
use Shimoning\LineNotify\Exceptions\MissingStateException;

final class GenerateAuthUri extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('auth:generate:uri');
        $this->setDescription('Generate Auth URi.');

        $this->addOption('client-id', 'i', InputOption::VALUE_OPTIONAL, 'Set ClientId. If not input, using env.', null);
        $this->addOption('redirect-uri', 'u', InputOption::VALUE_OPTIONAL, 'Set RedirectUri. If not input, using env.', null);
        $this->addOption('state', 's', InputOption::VALUE_OPTIONAL, 'Set State. If not input, generate automatically.', null);
        $this->addOption('response-mode', 'm', InputOption::VALUE_OPTIONAL, 'Set ResponseMode. Allowed "form_post" only.', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Generate Auth URL Start</info>');

        $clientId = $input->getOption('client-id') ?? $_ENV['CLIENT_ID'];
        if (empty($clientId)) {
            throw new MissingClientIdException();
        }

        $redirectUri = $input->getOption('redirect-uri') ?? $_ENV['REDIRECT_URI'];
        if (empty($redirectUri)) {
            throw new MissingRedirectUriException();
        }

        $state = $input->getOption('redirect-uri') ?? Auth::generateState(\uniqid(\rand(), true));
        if (empty($state)) {
            throw new MissingStateException();
        }

        $responseMode = $input->getOption('response-mode') ?? null;

        // generate
        $uri = Auth::generateAuthUri($clientId, $redirectUri, $state, $responseMode);
        if ($uri) {
            $output->writeln('<question> - Succeed !</question>');
            $output->writeln('   * url: ' . $uri);
        } else {
            $output->writeln('<comment> - Failed...</comment>');
        }

        $output->writeln('<info>Generate Auth URL End</info>');
        $output->writeln('---');
        return Command::SUCCESS;
    }
}

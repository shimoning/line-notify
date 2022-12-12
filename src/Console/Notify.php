<?php

namespace Shimoning\LineNotify\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Shimoning\LineNotify\Communicator\Api;
use Shimoning\LineNotify\Entity\Input\Image;
use Shimoning\LineNotify\Entity\Input\Sticker;
use Shimoning\LineNotify\ValueObject\ImageFile;
use Shimoning\LineNotify\ValueObject\Message;
use Shimoning\LineNotify\Exceptions\MissingAccessTokenException;
use Shimoning\LineNotify\ValueObject\ImageUri;

final class Notify extends Command
{
    protected OutputInterface $output;
    protected function configure(): void
    {
        parent::configure();

        $this->setName('notify');
        $this->setDescription('Send message to LINE.');

        $this->addOption('access-token', 't', InputOption::VALUE_OPTIONAL, 'Set AccessToken. If not input, using env.', null);

        $this->addOption('message', 'm', InputOption::VALUE_OPTIONAL, 'Message to notify.', 'Hello World!');
        $this->addOption('sticker', 's', InputOption::VALUE_OPTIONAL, 'Attach sticker. Input format is "PackageId-StickerId". example "446-1988"', null);
        $this->addOption('disabled-notification', 'd', InputOption::VALUE_OPTIONAL, 'Force to notification be disabled', false);

        // image
        $this->addOption('image-thumbnail', 'P', InputOption::VALUE_OPTIONAL, 'Attach image thumbnail. Input url.', null);
        $this->addOption('image-fullsize', 'I', InputOption::VALUE_OPTIONAL, 'Attach image full-sized. Input url.', null);
        $this->addOption('image-file', 'F', InputOption::VALUE_OPTIONAL, 'Upload image file. Input file full path.', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('---');
        $output->writeln('<info>Notify Start</info>');

        $accessToken = $input->getOption('access-token') ?? $_ENV['ACCESS_TOKEN'];
        if (empty($accessToken)) {
            throw new MissingAccessTokenException();
        }

        // sticker
        $sticker = null;
        if ($input->getOption('sticker')) {
            $ids = \explode('-', $input->getOption('sticker'));
            if (\count($ids) === 2) {
                $sticker = new Sticker($ids[0], $ids[1]);
            }
        }

        // image
        $image = null;
        $imageThumbnail = $input->getOption('image-thumbnail')
            ? new ImageUri($input->getOption('image-thumbnail'))
            : null;
        $imageFullSize = $input->getOption('image-fullsize')
            ? new ImageUri($input->getOption('image-fullsize'))
            : null;
        $imageFile = $input->getOption('image-file')
            ? new ImageFile($input->getOption('image-file'))
            : null;
        if (!empty($imageThumbnail) || !empty($imageFullSize) || !empty($imageFile)) {
            $image = new Image($imageThumbnail, $imageFullSize, $imageFile);
        }

        // request
        $result = Api::notify(
            $accessToken,
            new Message($input->getOption('message') ?? 'TEST.'),
            $sticker,
            $image,
            $input->getOption('disabled-notification'),
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

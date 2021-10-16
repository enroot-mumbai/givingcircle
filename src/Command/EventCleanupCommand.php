<?php

namespace App\Command;

use App\Repository\Transaction\TrnCircleEventsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventCleanupCommand extends Command
{
    private $eventRepository;

    protected static $defaultName = 'app:event:cleanup';

    public function __construct(TrnCircleEventsRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Set Event Status to Expired')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->eventRepository->countOldEvents();
        } else {
            $count = $this->eventRepository->changeEventStatus();
        }

        $io->success(sprintf('Events updated - "%d".', $count));

        return 0;
    }
}

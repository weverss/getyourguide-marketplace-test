<?php

namespace GetYourGuide\MarketplaceTest;

use DateTime;
use GetYourGuide\MarketplaceTest\ProductResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RetrieveProductCommand extends Command {

    protected $output;

    protected function configure()
    {
        $this
            ->setName('retrieve:products')
            ->setDescription('Retrieve available products')
            ->addArgument('start-time', InputArgument::REQUIRED, 'The start of the time period')
            ->addArgument('end-time', InputArgument::REQUIRED, 'The end of the time period')
            ->addArgument('number-of-travelers', InputArgument::REQUIRED, 'Number of travelers between 1 and 30');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = $this->parseStartTimeArgument(
            $input->getArgument('start-time'),
            $output
        );

        $endTime = $this->parseEndTimeArgument(
            $input->getArgument('end-time'),
            $startTime,
            $output
        );

        $numberOfTravelers = $this->parseNumberOfTravelers(
            $input->getArgument('number-of-travelers'),
            $output
        );

        $productResource = new ProductResource();

        $productsAvailable = $productResource->fetch(
            $startTime,
            $endTime,
            $numberOfTravelers
        );

        echo json_encode($productsAvailable, JSON_PRETTY_PRINT);
    }

    protected function parseStartTimeArgument($argument, OutputInterface $output)
    {
        if (!$startTime = $this->parseDateTimeArgument($argument)) {
            $output->writeln('<error>Invalid DateTime for argument "start-time"</error>');
            exit(1);
        }

        return $startTime;
    }

    protected function parseEndTimeArgument($argument, DateTime $startTime, OutputInterface $output)
    {
        if (!$endTime = $this->parseDateTimeArgument($argument)) {
            $output->writeln('<error>Invalid DateTime for argument "end-time"</error>');
            exit(1);
        }

        if ($endTime < $startTime) {
            $output->writeln('<error>"end-time" argument must be greater than "start-time"</error>');
            exit(1);
        }

        return $endTime;
    }

    protected function parseNumberOfTravelers($argument, OutputInterface $output)
    {
        if (!is_numeric($argument) || $argument < 1 || $argument > 30 ) {
            $output->writeln('<error>Invalid number of travelers. Must be integer between 1 and 30.</error>');
            exit(1);
        }

        return $argument;
    }

    protected function parseDateTimeArgument($argument)
    {
        return DateTime::createFromFormat('Y-m-d\\TH:i', $argument);
    }
}

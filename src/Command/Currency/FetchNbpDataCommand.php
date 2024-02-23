<?php

namespace App\Command\Currency;

use App\Exception\NbpApiBadRequestException;
use App\Exception\NbpApiResourceNotFoundException;
use App\Service\Currency\FetchNbpDataService;
use App\Service\Currency\SaveNbpDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'currency:fetch-nbp-data',
    description: 'Fetches data from NBP API and saves it into the database.'
)]
class FetchNbpDataCommand extends Command
{
    public function __construct(
        private readonly FetchNbpDataService $fetchNbpDataService,
        private readonly SaveNbpDataService $saveNbpDataService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $NbpDataArray = $this->fetchNbpDataService->fetchApiData();
        } catch (NbpApiResourceNotFoundException|NbpApiBadRequestException $exception) {
            $io->error('An error occurred while fetching NBP data: ' . $exception->getMessage());

            return Command::FAILURE;
        }

        $this->saveNbpDataService->saveData(...$NbpDataArray);
        $this->entityManager->flush();

        $io->success('NBP data fetched and saved into the database.');

        return Command::SUCCESS;
    }
}

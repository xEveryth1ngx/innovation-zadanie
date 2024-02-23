<?php

namespace App\Service\Currency;

use App\DTO\Currency\NbpApiCurrencyDto;
use App\Entity\Currency\Currency;
use Doctrine\ORM\EntityManagerInterface;

readonly class SaveNbpDataService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function saveData(NbpApiCurrencyDto ...$data): void
    {
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        foreach ($data as $currencyDTO) {
            $currencyObject = $currencyRepository->findOneByCode($currencyDTO->currencyCode);

            if (!$currencyObject) {
                $currencyObject = new Currency();

                $currencyObject->setName($currencyDTO->currencyName);
                $currencyObject->setCurrencyCode($currencyDTO->currencyCode);
            }

            $currencyObject->setExchangeRate($currencyDTO->exchangeRate);

            $this->entityManager->persist($currencyObject);
        }
    }
}
<?php

namespace App\Service\Currency;

use App\Entity\Currency\Currency;
use App\Repository\Currency\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;

class SaveNbpDataService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function saveNbpData(array $data): void
    {
        $currencyRepository = $this->entityManager->getRepository(Currency::class);

        foreach ($data as $currency) {
            $currencyName = $currency['currency'];
            $currencyCode = $currency['code'];
            $exchangeRate = $currency['mid'];

            $currencyObject = $currencyRepository->findOneByCode($currencyCode);

            if ($currencyObject) {
                $currencyObject->setExchangeRate($exchangeRate);
            } else {
                $currencyObject = new Currency();

                $currencyObject->setName($currencyName);
                $currencyObject->setCurrencyCode($currencyCode);
                $currencyObject->setExchangeRate($exchangeRate);
            }
        }
    }
}
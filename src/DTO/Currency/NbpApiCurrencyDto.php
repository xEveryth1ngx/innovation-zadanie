<?php

namespace App\DTO\Currency;

class NbpApiCurrencyDto
{
    public function __construct(
        public string $currencyName,
        public string $currencyCode,
        public string $exchangeRate,
    ) {
    }
}
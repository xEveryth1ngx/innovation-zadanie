<?php

namespace App\Interface\Currency;

interface SaveApiDataInterface
{
    public function saveData(array $data): void;
}
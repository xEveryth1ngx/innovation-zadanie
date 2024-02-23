<?php

namespace App\Service\Currency;

use App\Interface\Currency\FetchApiDataInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchNbpDataService implements FetchApiDataInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchApiData(): array
    {
        $response = $this->client->request('GET', 'http://api.nbp.pl/api/exchangerates/tables/a/?format=json');

        return $response->toArray();
    }
}
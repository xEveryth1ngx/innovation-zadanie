<?php

namespace App\Service\Currency;

use App\Exception\NbpApiBadRequestException;
use App\Exception\NbpApiResourceNotFoundException;
use App\Interface\Currency\FetchApiDataInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FetchNbpDataService implements FetchApiDataInterface
{
    private string $baseApiUrl;

    public function __construct(
        private readonly HttpClientInterface $client,
        ParameterBagInterface $parameterBag,
    ) {
        $this->baseApiUrl = $parameterBag->get('nbp.api.url');
    }

    /**
     * @throws NbpApiResourceNotFoundException
     * @throws NbpApiBadRequestException
     */
    public function fetchApiData(): array
    {
        $response = $this->client->request('GET', $this->baseApiUrl . '/exchangerates/tables/a/?format=json');

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NbpApiResourceNotFoundException('Resource not found.');
        }
        if ($response->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            throw new NbpApiBadRequestException('Bad request.');
        }

        return $response->toArray();
    }
}
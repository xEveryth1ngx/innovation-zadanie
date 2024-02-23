<?php

namespace App\Service\Currency;

use App\DTO\Currency\NbpApiCurrencyDto;
use App\Exception\NbpApiBadRequestException;
use App\Exception\NbpApiResourceNotFoundException;
use App\Interface\Currency\FetchApiDataInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
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
     * @return array<int, NbpApiCurrencyDto>
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

        $currencyData = $response->toArray();

        return array_map(
            fn (array $currency) => new NbpApiCurrencyDto(
                $currency['currency'],
                $currency['code'],
                $currency['mid'],
            ),
            $currencyData[0]['rates']
        );
    }
}
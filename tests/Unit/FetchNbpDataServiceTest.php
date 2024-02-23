<?php

namespace App\Tests\Unit;

use App\Exception\NbpApiBadRequestException;
use App\Exception\NbpApiResourceNotFoundException;
use App\Service\Currency\FetchNbpDataService;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FetchNbpDataServiceTest extends TestCase
{
    private readonly FetchNbpDataService $fetchNbpDataService;
    private readonly HttpClientInterface $client;
    private readonly ParameterBagInterface $parameterBag;

    public function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
    }

    public function testFetchApiData(): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->parameterBag->expects($this->once())
            ->method('get')
            ->with('nbp.api.url')
            ->willReturn('http://api.nbp.pl/api');

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'http://api.nbp.pl/api/exchangerates/tables/a/?format=json')
            ->willReturn($responseMock);

        $this->fetchNbpDataService = new FetchNbpDataService($this->client, $this->parameterBag);

        $data = [
            [
                'table' => 'A',
                'no' => '001/A/NBP/2021',
                'effectiveDate' => '2021-01-04',
                'rates' => [
                    [
                        'currency' => 'bat (Tajlandia)',
                        'code' => 'THB',
                        'mid' => 0.1234,
                    ],
                    [
                        'currency' => 'dolar amerykański',
                        'code' => 'USD',
                        'mid' => 3.6789,
                    ],
                ],
            ],
        ];

        $responseMock->expects($this->once())->method('toArray')->willReturn($data);

        $response = $this->fetchNbpDataService->fetchApiData();

        self::assertCount(2, $response);
    }


    /**
     * @dataProvider exceptionProvider
     */
    public function testFetchApiDataValidations(int $statusCode, string $exceptionMessage, string $exception): void
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->parameterBag->expects($this->once())
            ->method('get')
            ->with('nbp.api.url')
            ->willReturn('http://api.nbp.pl/api');

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'http://api.nbp.pl/api/exchangerates/tables/a/?format=json')
            ->willReturn($responseMock);

        $this->fetchNbpDataService = new FetchNbpDataService($this->client, $this->parameterBag);

        $responseMock->method('getStatusCode')->willReturn($statusCode);

        $this->expectExceptionMessage($exceptionMessage);
        $this->expectException($exception);

        $this->fetchNbpDataService->fetchApiData();
    }

    public function exceptionProvider(): array
    {
        return [
            'Api returns HTTP code 404' => [
                'statusCode' => Response::HTTP_BAD_REQUEST,
                'exceptionMessage' => 'Bad request.',
                'exception' => NbpApiBadRequestException::class,
            ],
            'Api returns HTTP code 400' => [
                'statusCode' => Response::HTTP_NOT_FOUND,
                'exceptionMessage' => 'Resource not found.',
                'exception' => NbpApiResourceNotFoundException::class,
            ],
        ];
    }
}
<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use GuzzleHttp\Client;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Flipt
{
    private const HTTP_STATUS_OK          = 200;
    private const PATH                    = '/api/v1/namespaces/';
    private const REQUEST_EVALUATE        = '/evaluate';
    private const REQUEST_EVALUATE_BATCH  = '/batch-evaluate';
    private const REQUEST_FLAGS           = '/flags';

    private Client $client;

    public static function create(string $baseUrl, int $timeout): self
    {
        if (mb_substr($baseUrl, -1) === '/') {
            $baseUrl = mb_substr($baseUrl, 0, -1);
        }

        $httpClient = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => $timeout,
        ]);

        return new static($httpClient);
    }

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function evaluate(EvaluateRequest $evaluateRequest, string $namespace = 'default'): EvaluateResponse
    {
        $json = json_encode($evaluateRequest);
        if ($json === false) {
            $json = '';
        }

        $response         = $this->client->post(
            self::PATH . $namespace . self::REQUEST_EVALUATE,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json,
            ]
        );
        $responseBody     = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== self::HTTP_STATUS_OK) {
            $message = 'http status code is not 200';
            if (array_key_exists('message', $responseBody)) {
                $message = $responseBody['message'];
            }
            throw new \Exception($message);
        }

        return new EvaluateResponse($responseBody);
    }

    /**
     * @param EvaluateRequest[] $evaluateRequests
     *
     * @throws ClientExceptionInterface
     */
    public function evaluateBatch(array $evaluateRequests, string $namespace = 'default'): EvaluateResponses
    {
        $json = json_encode(['requests' => $evaluateRequests]);

        if ($json === false) {
            $json = '';
        }

        $response             = $this->client->post(
            self::PATH . $namespace . self::REQUEST_EVALUATE_BATCH,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json,
            ]
        );

        $responseBody         = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== self::HTTP_STATUS_OK) {
            $message = 'http status code is not 200';
            if (array_key_exists('message', $responseBody)) {
                $message = $responseBody['message'];
            }
            throw new \Exception($message);
        }

        return new EvaluateResponses($responseBody);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function listFlags(string $namespace = 'default'): FlagResponses
    {
        $response     = $this->client->get(
            self::PATH . $namespace . self::REQUEST_FLAGS,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== self::HTTP_STATUS_OK) {
            $message = 'http status code is not 200';
            if (array_key_exists('message', $responseBody)) {
                $message = $responseBody['message'];
            }
            throw new \Exception($message);
        }

        return new FlagResponses($responseBody);
    }
}

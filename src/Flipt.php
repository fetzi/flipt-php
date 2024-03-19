<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use Fetzi\Flipt\models\BatchResponse;
use Fetzi\Flipt\models\BooleanResponse;
use Fetzi\Flipt\models\EvaluateRequest;
use Fetzi\Flipt\models\FlagResponses;
use Fetzi\Flipt\models\VariantResponse;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientExceptionInterface;

final class Flipt
{
    private const HTTP_STATUS_OK = 200;
    private const PATH           = '/api/v1/namespaces/';
    private const PATH_V2        = '/evaluate/v1';
    private const BOOLEAN        = '/boolean';
    private const VARIANT        = '/variant';
    private const BATCH          = '/batch';
    private const FLAGS          = '/flags';

    private Client $client;

    public static function create(string $baseUrl, float $timeout): self
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

    public function boolean(EvaluateRequest $evaluateRequest): BooleanResponse
    {
        $json = json_encode($evaluateRequest);
        if ($json === false) {
            $json = '';
        }

        $response         = $this->client->post(
            self::PATH_V2 . self::BOOLEAN,
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
            throw new \Exception('method: evaluate, ' . $message);
        }

        return new BooleanResponse($responseBody);
    }

    public function variant(EvaluateRequest $evaluateRequest): VariantResponse
    {
        $json = json_encode($evaluateRequest);
        if ($json === false) {
            $json = '';
        }

        $response         = $this->client->post(
            self::PATH_V2 . self::VARIANT,
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
            throw new \Exception('method: evaluate, ' . $message);
        }

        return new VariantResponse($responseBody);
    }

    /**
     * @param EvaluateRequest[] $evaluateRequests
     *
     * @throws ClientExceptionInterface
     */
    public function batch(array $evaluateRequests, string $namespace = 'default'): BatchResponse
    {
        $json = json_encode(['requests' => $evaluateRequests]);

        if ($json === false) {
            $json = '';
        }

        $response             = $this->client->post(
            self::PATH_V2 . self::BATCH,
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
            throw new \Exception('method: evaluate batch, ' . $message);
        }

        return new BatchResponse($responseBody);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function listFlags(string $namespace = 'default'): FlagResponses
    {
        $response     = $this->client->get(
            self::PATH . $namespace . self::FLAGS,
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
            throw new \Exception('method: list flags, ' . $message);
        }

        return new FlagResponses($responseBody);
    }
}

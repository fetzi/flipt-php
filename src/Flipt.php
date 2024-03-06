<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
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

    private HttpClient $client;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $baseURL;

    public static function create(string $baseUrl): self
    {
        return new static(
            HttpClientDiscovery::find(),
            Psr17FactoryDiscovery::findRequestFactory(),
            Psr17FactoryDiscovery::findStreamFactory(),
            $baseUrl
        );
    }

    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory, string $baseUrl)
    {
        $this->client          = $client;
        $this->requestFactory  = $requestFactory;
        $this->streamFactory   = $streamFactory;

        if (mb_substr($baseUrl, -1) === '/') {
            $baseUrl = mb_substr($baseUrl, 0, -1);
        }

        $this->baseURL       = $baseUrl;
    }

    public function evaluate(EvaluateRequest $evaluateRequest, string $namespace = 'default'): EvaluateResponse
    {
        $json = json_encode($evaluateRequest);
        if ($json === false) {
            $json = '';
        }

        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . self::PATH . $namespace . self::REQUEST_EVALUATE
        )
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));;

        $response = $this->client->sendRequest($request);
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

        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . self::PATH . $namespace . self::REQUEST_EVALUATE_BATCH
        )
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));

        $response             = $this->client->sendRequest($request);
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
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . self::PATH . $namespace . self::REQUEST_FLAGS
        )
            ->withHeader('Content-Type', 'application/json');

        $response     = $this->client->sendRequest($request);
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

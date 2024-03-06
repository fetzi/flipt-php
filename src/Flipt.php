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
    private const PATH                                   = '/api/v1/namespaces/';
    private const EVALUATE                               = '/evaluate';
    private const EVALUATE_BATCH                         = '/batch-evaluate';

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
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . self::PATH . $namespace . self::EVALUATE
        );

        $json = json_encode($evaluateRequest);

        if ($json === false) {
            $json = '';
        }

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));

        $response = $this->client->sendRequest($request);
        $data     = json_decode($response->getBody()->getContents(), true);

        return new EvaluateResponse($data);
    }

    /**
     * @param EvaluateRequest[] $evaluateRequests
     *
     * @throws ClientExceptionInterface
     */
    public function evaluateBatch(array $evaluateRequests, string $namespace = 'default'): EvaluateResponses
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . self::PATH . $namespace . self::EVALUATE_BATCH
        );

        $json = json_encode(['requests' => $evaluateRequests]);

        if ($json === false) {
            $json = '';
        }

        $request = $request
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($json));

        $response     = $this->client->sendRequest($request);
        $data         = json_decode($response->getBody()->getContents(), true);

        return new EvaluateResponses($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function listFlags(string $namespace = 'default'): FlagResponses
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            $this->baseURL . $namespace
        );

        $request = $request
            ->withHeader('Content-Type', 'application/json');

        $response     = $this->client->sendRequest($request);
        $responseBody = json_decode($response->getBody()->getContents(), true);

        return new FlagResponses($responseBody);
    }
}

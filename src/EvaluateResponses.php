<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

final class EvaluateResponses
{
    private string $requestID;
    private float $requestDurationMillis;
    private array $responses = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->requestID             = $data['requestId'] ?? '';
        $this->requestDurationMillis = $data['requestDurationMillis'] ?? 0;
        foreach ($data['responses'] as $response) {
            $this->responses[] = new EvaluateResponse($response);
        }
    }

    public function getRequestID(): string
    {
        return $this->requestID;
    }

    public function getrequestDurationMillis(): float
    {
        return $this->requestDurationMillis;
    }

    /**
     * @return EvaluateResponse[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getResponseFromIndex(int $index): EvaluateResponse
    {
        return $this->responses[$index];
    }
}

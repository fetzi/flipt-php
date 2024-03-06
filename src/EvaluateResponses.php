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

    private function getRequestID(): string
    {
        return $this->requestID;
    }

    private function getrequestDurationMillis(): float
    {
        return $this->requestDurationMillis;
    }

    /**
     * @return EvaluateResponse[]
     */
    private function getResponses(): array
    {
        return $this->responses;
    }

    public function getResponseFromIndex(int $index): EvaluateResponse
    {
        return $this->responses[$index];
    }
}

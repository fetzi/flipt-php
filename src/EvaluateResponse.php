<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

final class EvaluateResponse
{
    private string $requestId = '';
    private string $entityId  = '';

    /**
     * @var array<mixed, mixed>
     */
    private array $context               = [];
    private bool $match                  = false;
    private string $flagKey              = '';
    private string $segmentKey           = '';
    private \DateTime $timestamp;
    private string $value                = '';
    private float $requestDurationMillis = 0.0;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->requestId             = $data['requestId'] ?? '';
        $this->entityId              = $data['entityId'] ?? '';
        $this->context               = $data['context'] ?? [];
        $this->match                 = $data['match'] ?? false;
        $this->flagKey               = $data['flagKey'] ?? '';
        $this->segmentKey            = $data['segmentKey'] ?? '';
        $this->timestamp             = isset($data['timestamp']) ? $this->parseDataTimestamp($data['timestamp']) : new \DateTime('now');
        $this->value                 = $data['value'] ?? '';
        $this->requestDurationMillis = $data['requestDurationMillis'] ?? 0.0;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function isMatch(): bool
    {
        return $this->match;
    }

    public function getFlagKey(): string
    {
        return $this->flagKey;
    }

    public function getSegmentKey(): string
    {
        return $this->segmentKey;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getVariant(): string
    {
        return $this->getValue();
    }

    public function getRequestDurationMillis(): float
    {
        return $this->requestDurationMillis;
    }

    private function parseDataTimestamp(string $dataTimestamp): \DateTime
    {
        $timestamp = \DateTime::createFromFormat('Y-m-d\TH:i:s.uu\Z', $dataTimestamp);

        if (!$timestamp) {
            $timestamp = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $dataTimestamp);
        }

        if (!$timestamp) {
            $timestamp = new \DateTime('now');
        }

        return $timestamp;
    }
}

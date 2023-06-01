<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use DateTime;

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
    private DateTime $timestamp;
    private string $value                = '';
    private float $requestDurationMillis = 0.0;

    private bool $hasError       = false;
    private string $errorMessage = '';

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        if (array_key_exists('error', $data)) {
            $this->hasError     = true;
            $this->errorMessage = $data['message'] ?? '';

            return;
        }

        $this->requestId             = $data['requestId'] ?? '';
        $this->entityId              = $data['entityId'] ?? '';
        $this->context               = $data['context'] ?? [];
        $this->match                 = $data['match'] ?? false;
        $this->flagKey               = $data['flagKey'] ?? '';
        $this->segmentKey            = $data['segmentKey'] ?? '';
        $this->timestamp             = $data['timestamp'] ? DateTime::createFromFormat('Y-m-d\TH:i:s.uu\Z', $data['timestamp']) : DateTime('now');
        $this->value                 = $data['value'] ?? '';
        $this->requestDurationMillis = $data['requestDurationMillis'] ?? 0.0;
    }

    public function hasError(): bool
    {
        return $this->hasError;
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
        if ($this->hasError()) {
            return false;
        }

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

    public function getTimestamp(): DateTime
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
}

<?php

declare(strict_types=1);

namespace Fetzi\Flipt\models;

final class VariantResponse
{
    public const REASON_MATCH            = 'MATCH_EVALUATION_REASON';
    public const REASON_UNKNOWN          = 'UNKNOWN_EVALUATION_REASON';

    private bool $match;
    private array $segmentKeys;
    private string $reason;
    private string $variantKey;
    private string $variantAttachment;
    private string $requestId;
    private float $requestDurationMillis;
    private \DateTime $timestamp;
    private string $flagKey;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->match                 = $data['match'];
        $this->segmentKeys           = $data['segmentKeys'];
        $this->reason                = $data['reason'];
        $this->variantKey            = $data['variantKey'];
        $this->variantAttachment     = $data['variantAttachment'];
        $this->requestId             = $data['requestId'] ?? '';
        $this->requestDurationMillis = $data['requestDurationMillis'] ?? 0.0;
        $this->timestamp             = isset($data['timestamp']) ? $this->parseDataTimestamp($data['timestamp']) : new \DateTime('now');
        $this->flagKey               = $data['flagKey'] ?? '';
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

    public function isMatch(): bool
    {
        return $this->match;
    }

    public function getSegmentKeys(): array
    {
        return $this->segmentKeys;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getVariantKey(): string
    {
        return $this->variantKey;
    }

    public function getVariantAttachment(): string
    {
        return $this->variantAttachment;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getRequestDurationMillis(): float
    {
        return $this->requestDurationMillis;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getFlagKey(): string
    {
        return $this->flagKey;
    }
}

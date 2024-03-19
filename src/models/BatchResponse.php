<?php

declare(strict_types=1);

namespace Fetzi\Flipt\models;

final class BatchResponse
{
    public const RESPONSE_TYPE_BOOLEAN  = 'BOOLEAN_EVALUATION_RESPONSE_TYPE';
    public const RESPONSE_TYPE_VARIANT  = 'VARIANT_EVALUATION_RESPONSE_TYPE';

    /**
     * @var BooleanResponse[]
     */
    private array $booleanResponse      = [];

    /**
     * @var VariantResponse[]
     */
    private array $variantResponse      = [];
    private string $requestID;
    private float $requestDurationMillis;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->requestID             = $data['requestId'] ?? '';
        $this->requestDurationMillis = $data['requestDurationMillis'] ?? 0;
        foreach ($data['responses'] as $response) {
            if (array_key_exists('type', $response)) {
                if ($response['type'] === self::RESPONSE_TYPE_BOOLEAN) {
                    $this->booleanResponse[] = new BooleanResponse($response['booleanResponse']);
                }
                if ($response['type'] === self::RESPONSE_TYPE_VARIANT) {
                    $this->variantResponse[] = new VariantResponse($response['variantResponse']);
                }
            }
        }
    }

    /**
     * @return BooleanResponse[]
     */
    public function getBooleans(): array
    {
        return $this->booleanResponse;
    }

    /**
     * @return VariantResponse[]
     */
    public function getVariants(): array
    {
        return $this->variantResponse;
    }

    public function getRequestID(): string
    {
        return $this->requestID;
    }

    public function getRequestDurationMillis(): float
    {
        return $this->requestDurationMillis;
    }
}

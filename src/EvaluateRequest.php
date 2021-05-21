<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

use JsonSerializable;

final class EvaluateRequest implements JsonSerializable
{
    private string $flagKey;
    private string $entityId;

    /**
     * @var array<string, mixed>
     */
    private array $context;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(string $flagKey, string $entityId, array $context)
    {
        $this->flagKey  = $flagKey;
        $this->entityId = $entityId;
        $this->context  = $context;
    }

    public function getFlagKey(): string
    {
        return $this->flagKey;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return array<string,mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'flag_key'  => $this->flagKey,
            'entity_id' => $this->entityId,
            'context'   => (object) $this->context,
        ];
    }
}

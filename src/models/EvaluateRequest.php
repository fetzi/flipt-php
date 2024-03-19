<?php

declare(strict_types=1);

namespace Fetzi\Flipt\models;

final class EvaluateRequest implements \JsonSerializable
{
    private string $namespace;
    private string $flagKey;
    private string $entityId;

    /**
     * @var array<string, mixed>
     */
    private array $context;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(string $namespace, string $flagKey, string $entityId, array $context)
    {
        $this->namespace = $namespace;
        $this->flagKey   = $flagKey;
        $this->entityId  = $entityId;
        $this->context   = $context;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
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
            'namespaceKey' => $this->namespace,
            'flagKey'      => $this->flagKey,
            'entityId'     => $this->entityId,
            'context'      => (object) $this->context,
        ];
    }
}

<?php

declare(strict_types=1);

namespace Fetzi\Flipt\models;

final class FlagResponse
{
    public const BOOLEAN_FLAG_TYPE = 'BOOLEAN_FLAG_TYPE';
    public const VARIANT_FLAG_TYPE = 'VARIANT_FLAG_TYPE';
    private string $key;
    private string $name;
    private string $description;
    private bool $enabled;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private array $variant;
    private string $namespaceKey;
    private string $type;

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->key          = $data['key'] ?? '';
        $this->name         = $data['name'] ?? '';
        $this->description  = $data['description'] ?? '';
        $this->enabled      = $data['enabled'] ?? false;
        $this->createdAt    = new \DateTime($data['createdAt']) ?? null;
        $this->updatedAt    = new \DateTime($data['updatedAt']) ?? null;
        $this->variant      = $data['variants'] ?? [];
        $this->namespaceKey = $data['namespaceKey'] ?? '';
        $this->type         = $data['type'] ?? '';
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getVariants(): array
    {
        return $this->variant;
    }

    public function getNamespaceKey(): string
    {
        return $this->namespaceKey;
    }

    public function getType(): string
    {
        return $this->type;
    }
}

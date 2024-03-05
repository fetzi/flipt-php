<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

final class FlagResponse
{
    private bool $hasError       = false;
    private string $errorMessage = '';
    private string $key;
    private string $name;
    private string $description;
    private bool $enabled;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private array $variant;
    private string $namespaceKey;

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        if (array_key_exists('error', $data)) {
            $this->hasError     = true;
            $this->errorMessage = $data['message'] ?? '';

            return;
        }

        $this->key          = $data['key'] ?? '';
        $this->name         = $data['name'] ?? '';
        $this->description  = $data['description'] ?? '';
        $this->enabled      = $data['enabled'] ?? false;
        $this->createdAt    = new \DateTime($data['createdAt']) ?? null;
        $this->updatedAt    = new \DateTime($data['updatedAt']) ?? null;
        $this->variant      = $data['variants'] ?? [];
        $this->namespaceKey = $data['namespaceKey'] ?? '';
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
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
}

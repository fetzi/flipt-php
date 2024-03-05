<?php

declare(strict_types=1);

namespace Fetzi\Flipt;

final class FlagResponses
{
    private bool $hasError       = false;
    private string $errorMessage = '';
    private array $flags         = [];
    private string $nextPageToken;
    private int $totalCount;

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

        $this->nextPageToken = $data['nextPageToken'] ?? '';
        $this->totalCount    = $data['totalCount'] ?? 0;
        foreach ($data['flags'] as $flag) {
            $this->flags[] = new FlagResponse($flag);
        }
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return FlagResponse[]
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getFlagFromIndex(int $index): FlagResponse
    {
        return $this->flags[$index];
    }

    public function getNextPageToken(): string
    {
        return $this->nextPageToken;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}

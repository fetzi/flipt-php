<?php

declare(strict_types=1);

namespace Fetzi\Flipt\models;

final class FlagResponses
{
    /**
     * @var FlagResponse[]
     */
    private array $flags = [];
    private string $nextPageToken;
    private int $totalCount;

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->nextPageToken = $data['nextPageToken'] ?? '';
        $this->totalCount    = $data['totalCount'] ?? 0;
        foreach ($data['flags'] as $flag) {
            $this->flags[] = new FlagResponse($flag);
        }
    }

    /**
     * @return FlagResponse[]
     */
    public function getFlags(): array
    {
        return $this->flags;
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

<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Collection;

class PaginationResult
{
    /**
     * @param  Collection<int, mixed>  $data
     */
    public function __construct(
        public readonly Collection $data,
        public readonly int $currentPage,
        public readonly int $lastPage,
        public readonly int $total,
        public readonly int $perPage,
    ) {}

    /** @return array<string, int> */
    public function meta(): array
    {
        return [
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
            'per_page' => $this->perPage,
        ];
    }
}

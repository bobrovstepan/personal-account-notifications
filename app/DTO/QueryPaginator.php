<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Database\Eloquent\Builder;

class QueryPaginator
{
    public static function paginate(Builder $query, int $perPage, int $page): PaginationResult
    {
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return new PaginationResult(
            data: collect($paginator->items()),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
        );
    }
}

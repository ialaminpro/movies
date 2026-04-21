<?php

declare(strict_types=1);

namespace App\Search;

final readonly class MovieSearchResult
{
    public function __construct(public int $id, public string $title)
    {
    }
}

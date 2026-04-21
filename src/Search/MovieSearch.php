<?php

declare(strict_types=1);

namespace App\Search;

interface MovieSearch
{
    /** @return list<MovieSearchResult> */
    public function search(string $query, int $limit = 20): array;
}

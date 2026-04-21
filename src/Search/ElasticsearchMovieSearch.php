<?php

declare(strict_types=1);

namespace App\Search;

use App\Entity\Movie;
use FOS\ElasticaBundle\Finder\FinderInterface;

final readonly class ElasticsearchMovieSearch implements MovieSearch
{
    public function __construct(private FinderInterface $finder)
    {
    }

    public function search(string $query, int $limit = 20): array
    {
        $query = trim($query);
        if ('' === $query) {
            return [];
        }

        try {
            $movies = $this->finder->find($query, max(1, min($limit, 50)));
        } catch (\Throwable $exception) {
            throw new SearchUnavailable('Elasticsearch query failed.', 0, $exception);
        }

        $results = [];
        foreach ($movies as $movie) {
            if (!$movie instanceof Movie) {
                throw new SearchUnavailable('Search returned an unexpected result type.');
            }

            $id = $movie->getId();
            if (null === $id) {
                throw new SearchUnavailable('Search returned a movie without an identifier.');
            }

            $results[] = new MovieSearchResult($id, $movie->getTitle());
        }

        return $results;
    }
}

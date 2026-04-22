<?php

declare(strict_types=1);

namespace App\Tests\Functional\Movie;

use App\Search\MovieSearch;
use App\Search\MovieSearchResult;
use App\Search\SearchUnavailable;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MovieSearchTest extends FunctionalTestCase
{
    public function testSearchReturnsMappedSuggestions(): void
    {
        static::getContainer()->set(MovieSearch::class, new class implements MovieSearch {
            public function search(string $query, int $limit = 20): array
            {
                return [new MovieSearchResult(7, 'Heat')];
            }
        });

        $this->client->request('GET', '/movies/search?q=crime');

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString(
            '{"suggestions":[{"id":7,"title":"Heat"}]}',
            (string) $this->client->getResponse()->getContent(),
        );
    }

    public function testSearchCanReturnNoResults(): void
    {
        static::getContainer()->set(MovieSearch::class, new class implements MovieSearch {
            public function search(string $query, int $limit = 20): array
            {
                return [];
            }
        });

        $this->client->request('GET', '/movies/search?q=unknown');

        self::assertResponseIsSuccessful();
        self::assertSame('{"suggestions":[]}', $this->client->getResponse()->getContent());
    }

    public function testProviderFailureReturnsGenericServiceUnavailableResponse(): void
    {
        static::getContainer()->set(MovieSearch::class, new class implements MovieSearch {
            public function search(string $query, int $limit = 20): array
            {
                throw new SearchUnavailable('sensitive connection details');
            }
        });

        $this->client->request('GET', '/movies/search?q=heat');

        self::assertResponseStatusCodeSame(Response::HTTP_SERVICE_UNAVAILABLE);
        self::assertStringNotContainsString('sensitive', (string) $this->client->getResponse()->getContent());
    }
}

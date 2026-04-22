<?php

declare(strict_types=1);

namespace App\Tests\Unit\Search;

use App\Entity\Movie;
use App\Search\ElasticsearchMovieSearch;
use App\Search\SearchUnavailable;
use FOS\ElasticaBundle\Finder\FinderInterface;
use PHPUnit\Framework\TestCase;

final class ElasticsearchMovieSearchTest extends TestCase
{
    public function testEmptyQueryDoesNotCallElasticsearch(): void
    {
        $finder = $this->createMock(FinderInterface::class);
        $finder->expects(self::never())->method('find');

        self::assertSame([], (new ElasticsearchMovieSearch($finder))->search('  '));
    }

    public function testResultsAreMappedAndLimitIsBounded(): void
    {
        $movie = (new Movie())->setTitle('Heat');
        $id = new \ReflectionProperty($movie, 'id');
        $id->setValue($movie, 42);

        $finder = $this->createMock(FinderInterface::class);
        $finder->expects(self::once())
            ->method('find')
            ->with('crime', 50)
            ->willReturn([$movie]);

        $results = (new ElasticsearchMovieSearch($finder))->search(' crime ', 1000);

        self::assertCount(1, $results);
        self::assertSame(42, $results[0]->id);
        self::assertSame('Heat', $results[0]->title);
    }

    public function testProviderFailureIsWrapped(): void
    {
        $finder = $this->createMock(FinderInterface::class);
        $finder->method('find')->willThrowException(new \RuntimeException('connection details'));

        $this->expectException(SearchUnavailable::class);
        $this->expectExceptionMessage('Elasticsearch query failed.');

        (new ElasticsearchMovieSearch($finder))->search('heat');
    }
}

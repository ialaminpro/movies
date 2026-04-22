<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Actor;
use App\Entity\Movie;
use PHPUnit\Framework\TestCase;

final class MovieTest extends TestCase
{
    public function testActorRelationshipIsKeptConsistent(): void
    {
        $movie = new Movie();
        $actor = (new Actor())->setName('Amy Adams');

        $movie->addActor($actor);
        self::assertTrue($movie->getActors()->contains($actor));

        $movie->removeActor($actor);
        self::assertFalse($movie->getActors()->contains($actor));
    }
}

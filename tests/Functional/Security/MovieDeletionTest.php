<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Entity\Movie;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MovieDeletionTest extends FunctionalTestCase
{
    public function testGetCannotDeleteMovie(): void
    {
        $movie = $this->createMovie();
        $this->client->loginUser($this->createUser(true));

        $this->client->request('GET', '/movies/'.$movie->getId());

        self::assertResponseIsSuccessful();
        self::assertNotNull($this->entityManager->find(Movie::class, $movie->getId()));
    }

    public function testAnonymousAndRegularUsersCannotDelete(): void
    {
        $movie = $this->createMovie();

        $this->client->request('POST', '/movies/'.$movie->getId(), ['_token' => 'invalid']);
        self::assertResponseRedirects('/login');

        $this->client->loginUser($this->createUser());
        $this->client->request('POST', '/movies/'.$movie->getId(), ['_token' => 'invalid']);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertNotNull($this->entityManager->find(Movie::class, $movie->getId()));
    }

    public function testMissingAndInvalidCsrfTokensAreRejected(): void
    {
        $movie = $this->createMovie();
        $this->client->loginUser($this->createUser(true));

        $this->client->request('POST', '/movies/'.$movie->getId());
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->request('POST', '/movies/'.$movie->getId(), ['_token' => 'invalid']);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertNotNull($this->entityManager->find(Movie::class, $movie->getId()));
    }

    public function testAdminCanDeleteWithValidCsrfToken(): void
    {
        $movie = $this->createMovie();
        $id = $movie->getId();
        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/'.$id);
        $form = $crawler->selectButton('Delete Movie')->form();

        $this->client->submit($form);

        self::assertResponseRedirects('/movies');
        self::assertNull($this->entityManager->find(Movie::class, $id));
    }

    private function createMovie(): Movie
    {
        $movie = (new Movie())
            ->setTitle('The Conversation')
            ->setReleaseYear(1974)
            ->setDescription('A surveillance expert confronts the consequences of his work.')
            ->setImagePath('/uploads/test.jpg');
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $movie;
    }
}

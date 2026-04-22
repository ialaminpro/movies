<?php

declare(strict_types=1);

namespace App\Tests\Functional\Movie;

use App\Entity\Movie;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

final class MovieCrudTest extends FunctionalTestCase
{
    public function testListDetailAndMissingMovie(): void
    {
        $movie = $this->createMovie();

        $this->client->request('GET', '/movies');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Arrival');

        $this->client->request('GET', '/movies/'.$movie->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Arrival');

        $this->client->request('GET', '/movies/999999');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testOnlyAdminsCanCreateMovies(): void
    {
        $this->client->request('GET', '/movies/create');
        self::assertResponseRedirects('/login');

        $this->client->loginUser($this->createUser());
        $this->client->request('GET', '/movies/create');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->loginUser($this->createUser(true));
        $this->client->request('GET', '/movies/create');
        self::assertResponseIsSuccessful();
    }

    public function testAdminCanCreateMovieWithValidatedImage(): void
    {
        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/create');
        $image = $this->temporaryImage();
        $form = $crawler->selectButton('Submit Post')->form([
            'movie_form[title]' => 'Moon',
            'movie_form[releaseYear]' => '2009',
            'movie_form[description]' => 'A grounded science-fiction drama.',
            'movie_form[image]' => new UploadedFile($image, 'poster.png', 'image/png', null, true),
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects();
        self::assertNotNull($this->entityManager->getRepository(Movie::class)->findOneBy(['title' => 'Moon']));
    }

    public function testInvalidCreateFormDoesNotPersist(): void
    {
        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/create');
        $form = $crawler->selectButton('Submit Post')->form([
            'movie_form[title]' => '',
            'movie_form[releaseYear]' => '1200',
            'movie_form[description]' => 'Invalid.',
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSame(0, $this->entityManager->getRepository(Movie::class)->count([]));
    }

    public function testNonImageUploadIsRejected(): void
    {
        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/create');
        $path = tempnam(sys_get_temp_dir(), 'not-an-image-');
        self::assertIsString($path);
        file_put_contents($path, 'plain text');
        $form = $crawler->selectButton('Submit Post')->form([
            'movie_form[title]' => 'Invalid Poster',
            'movie_form[releaseYear]' => '2020',
            'movie_form[description]' => 'Should not persist.',
            'movie_form[image]' => new UploadedFile($path, 'poster.txt', 'text/plain', null, true),
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('form', 'Please upload a valid JPEG, PNG, or WebP image.');
    }

    public function testOversizedImageIsRejected(): void
    {
        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/create');
        $path = tempnam(sys_get_temp_dir(), 'large-image-');
        self::assertIsString($path);
        $handle = fopen($path, 'wb');
        self::assertIsResource($handle);
        fseek($handle, 6 * 1024 * 1024);
        fwrite($handle, '0');
        fclose($handle);
        $form = $crawler->selectButton('Submit Post')->form([
            'movie_form[title]' => 'Large Poster',
            'movie_form[releaseYear]' => '2020',
            'movie_form[description]' => 'Should not persist.',
            'movie_form[image]' => new UploadedFile($path, 'poster.png', 'image/png', null, true),
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('form', 'too large');
    }

    public function testOnlyAdminsCanEditMovies(): void
    {
        $movie = $this->createMovie();

        $this->client->request('GET', '/movies/'.$movie->getId().'/edit');
        self::assertResponseRedirects('/login');

        $this->client->loginUser($this->createUser());
        $this->client->request('GET', '/movies/'.$movie->getId().'/edit');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $this->client->loginUser($this->createUser(true));
        $crawler = $this->client->request('GET', '/movies/'.$movie->getId().'/edit');
        $form = $crawler->selectButton('Submit Post')->form([
            'movie_form[title]' => 'Arrival: Updated',
            'movie_form[releaseYear]' => '2016',
            'movie_form[description]' => 'Updated description.',
        ]);
        $this->client->submit($form);

        self::assertResponseRedirects('/movies/'.$movie->getId());
        $this->entityManager->clear();
        self::assertSame(
            'Arrival: Updated',
            $this->entityManager->find(Movie::class, $movie->getId())?->getTitle(),
        );
    }

    private function createMovie(): Movie
    {
        $movie = (new Movie())
            ->setTitle('Arrival')
            ->setReleaseYear(2016)
            ->setDescription('A linguist works with the military to communicate with alien lifeforms.')
            ->setImagePath('https://example.com/arrival.jpg');

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $movie;
    }

    private function temporaryImage(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'movie-poster-');
        self::assertIsString($path);
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true));

        return $path;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use App\Search\MovieSearch;
use App\Search\SearchUnavailable;
use App\Storage\MovieImageStorage;
use App\Storage\StorageException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/movies')]
final class MoviesController extends AbstractController
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MovieSearch $movieSearch,
        private readonly MovieImageStorage $imageStorage,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('', name: 'movies', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('movies/index.html.twig', [
            'movies' => $this->movieRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/search', name: 'movie_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query->get('q', ''));

        try {
            $results = $this->movieSearch->search($query, 10);
        } catch (SearchUnavailable $exception) {
            $this->logger->warning('Movie search is unavailable.', ['exception' => $exception]);

            return $this->json(['error' => 'Search is temporarily unavailable.'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->json([
            'suggestions' => array_map(
                static fn ($result): array => ['id' => $result->id, 'title' => $result->title],
                $results,
            ),
        ]);
    }

    #[Route('/create', name: 'create_movie', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie, ['image_required' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image instanceof UploadedFile) {
                try {
                    $movie->setImagePath($this->imageStorage->upload($image));
                    $this->entityManager->persist($movie);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Movie created.');

                    return $this->redirectToRoute('show_movie', ['id' => $movie->getId()]);
                } catch (StorageException) {
                    $form->get('image')->addError(new FormError('The image could not be stored. Please try again.'));
                }
            }
        }

        return $this->render(
            'movies/create.html.twig',
            ['form' => $form],
            new Response(status: $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK),
        );
    }

    #[Route('/{id}', name: 'show_movie', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function show(Movie $movie): Response
    {
        return $this->render('movies/show.html.twig', ['movie' => $movie]);
    }

    #[Route('/{id}/edit', name: 'edit_movie', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Movie $movie, Request $request): Response
    {
        $oldImagePath = $movie->getImagePath();
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            try {
                if ($image instanceof UploadedFile) {
                    $movie->setImagePath($this->imageStorage->upload($image));
                }

                $this->entityManager->flush();

                if ($image instanceof UploadedFile) {
                    $this->imageStorage->delete($oldImagePath);
                }

                $this->addFlash('success', 'Movie updated.');

                return $this->redirectToRoute('show_movie', ['id' => $movie->getId()]);
            } catch (StorageException) {
                $form->get('image')->addError(new FormError('The image could not be stored. Please try again.'));
            }
        }

        return $this->render(
            'movies/edit.html.twig',
            ['movie' => $movie, 'form' => $form],
            new Response(status: $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK),
        );
    }

    #[Route('/{id}', name: 'delete_movie', requirements: ['id' => '\\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Movie $movie, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$movie->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $imagePath = $movie->getImagePath();
        $this->entityManager->remove($movie);
        $this->entityManager->flush();
        $this->imageStorage->delete($imagePath);
        $this->addFlash('success', 'Movie deleted.');

        return $this->redirectToRoute('movies');
    }
}

<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    #[Route('/movies', name: 'movies')]
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Movie::class);
        $movies = $repository->findAll();

        dd($movies);

        return $this->render('index.html.twig', array( 'movies' => $movies));
    }
}

<?php

namespace App\Controller;

use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    private $userFinder;

    public function __construct(TransformedFinder $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function list()
    {
        $users = $this->userFinder->find([]);
        return $this->render('user/index.html.twig', [
            'results' => $users,
        ]);
    }

    #[Route('/users/search/{email}', methods:['GET'], name: 'user_search')]
    public function search($email)
    {

        $query = new MatchQuery();
        $query->setFieldQuery('email', $email);

        $results = $this->userFinder->find($query);

        return $this->render('user/search.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/users/show/{id}', methods:['GET'], name: 'user_detail')]
    public function show($id)
    {

        $query = new MatchQuery();
        $query->setFieldQuery('id', $id);

        $results = $this->userFinder->find($query);

        return $this->render('user/search.html.twig', [
            'results' => $results,
        ]);
    }
}


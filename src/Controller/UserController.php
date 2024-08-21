<?php

namespace App\Controller;

use App\Document\Rental;
use App\Form\RentalType;
use App\Form\UserType;
use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;


class UserController extends AbstractController
{
    private $_documentManager;

    private $_userFinder;

    private $_logger;

    /**
     * __construct -
     *
     * This function is responsible for initializing the UserController class.
     *
     * @param  DocumentManager  $_documentManager  - The document manager
     * @param  TransformedFinder  $_userFinder  - The user transformed finder
     * @param  LoggerInterface  $_logger  - The logger interface
     *
     * @return void
     */
    public function __construct(DocumentManager $_documentManager, TransformedFinder $_userFinder, LoggerInterface $_logger)
    {
        $this->_documentManager = $_documentManager;
        $this->_userFinder = $_userFinder;
        $this->_logger = $_logger;
    }

    /**
     * Create action to create a new user
     *
     * @param  Request  $request  - The request object
     *
     * @return Response - The response object
     */
    #[Route('/users/create', name: 'user_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        // Create a new rental instance
        $user = new User();
        // print request
        $this->_logger->info('Request: '.$request->getContent());

        // Create a form to create a new user
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted and valid, persist the rental and redirect to the users page
        if ($form->isSubmitted() && $form->isValid()) {
            $this->_documentManager->persist($user);
            $this->_documentManager->flush();

            return $this->redirectToRoute('app_home_index');
        }

        // Render the create rental page
        return $this->render(
            'user/create.html.twig', [
                'userForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function list()
    {
        $results = $this->_userFinder->find([]);
        return $this->render('user/search.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/users/search/{email}', methods:['GET'], name: 'user_search')]
    public function search($email)
    {

        $query = new MatchQuery();
        $query->setFieldQuery('email', $email);

        $results = $this->_userFinder->find($query);

        return $this->render('user/search.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/users/show/{id}', methods:['GET'], name: 'user_detail')]
    public function show($id)
    {

        $query = new MatchQuery();
        $query->setFieldQuery('id', $id);

        $results = $this->_userFinder->find($query);

        return $this->render('user/search.html.twig', [
            'results' => $results,
        ]);
    }
}


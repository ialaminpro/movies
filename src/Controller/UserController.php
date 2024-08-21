<?php

namespace App\Controller;

use App\Form\UserType;
use Elastica\Query\MatchQuery;
use Elastica\Suggest;
use Elastica\Suggest\Completion;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\User;
use Elastica\Search;
use Elastica\Query;


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

    // Index action to display all the users
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Fetch all the users
        $users = $this->_documentManager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', ['users' => $users]);
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

            return $this->redirectToRoute('user_index');
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

    #[Route('/users/search/suggest', methods:['GET'], name: 'user_search_suggest')]
    public function suggest(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        $queryString = '*' . $query . '*';
        $searchParams = [
            'query' => [
                'query_string' => [
                    'query' => $queryString,
                    'fields' => ['firstName', 'lastName']
                ]
            ]
        ];

        // Perform the search
        $results = $this->_userFinder->find($searchParams);

         return new JsonResponse([
            'suggestions' => array_map(function($item) {
                return ['firstName' => $item->getFirstName() . ' '. $item->getLastName()];
            }, $results),
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


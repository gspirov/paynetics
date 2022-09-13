<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    '/projects',
    name: 'app_project_'
)]
class ProjectController extends AbstractController
{
    #[Route(
        '/',
        name: 'index',
        methods: [Request::METHOD_GET]
    )]
    public function index(
        ProjectRepository $projectRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $qb = $projectRepository->getAllOnQueryBuilder();

        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 10);

        return $this->render('project/index.html.twig', compact('pagination'));
    }

    #[Route(
        '/',
        name: 'add',
        methods: [
            Request::METHOD_GET,
            Request::METHOD_POST
        ]
    )]
    public function add(
        Request $request
    ): Response {

    }
}

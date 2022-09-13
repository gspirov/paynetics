<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    '/{project}/task',
    name: 'app_task_'
)]
class TaskController extends AbstractController
{
    #[Route(
        '/',
        name: 'index',
        methods: [Request::METHOD_GET]
    )]
    public function index(
        Project $project,
        Request $request,
        TaskRepository $taskRepository,
        PaginatorInterface $paginator
    ): Response {
        $qb = $taskRepository->getAllByProjectOnQueryBuilder($project);

        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 10);

        return $this->render('task/index.html.twig', compact('pagination', 'project'));
    }
}

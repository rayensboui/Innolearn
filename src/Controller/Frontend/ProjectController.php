<?php

namespace App\Controller\Frontend;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projects')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_frontend_project_index')]
    public function index(
        Request $request,
        ProjectRepository $projectRepository
    ): Response
    {
        // Récupérer tous les projets actifs (ou filtrer selon vos besoins)
        $projects = $projectRepository->findBy(
            ['status' => 'active'], // Filtrer seulement les projets actifs
            ['createdAt' => 'DESC'] // Trier par date de création décroissante
        );

        return $this->render('frontend/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/{id}', name: 'app_frontend_project_show', requirements: ['id' => '\d+'])]
    public function show(Project $project): Response
    {
        return $this->render('frontend/project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
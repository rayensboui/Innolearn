<?php

namespace App\Controller\Backend;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backend/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'app_backend_project_index', methods: ['GET'])]
    public function index(
        ProjectRepository $projectRepository,
        Request $request
    ): Response
    {
        // Récupérer les paramètres de tri
        $sortBy = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');
        
        // Récupérer le terme de recherche
        $search = $request->query->get('search', '');
        
        // Récupérer les projets avec tri et recherche
        $projects = $projectRepository->findAllWithSortAndSearch($sortBy, $order, $search);
        
        return $this->render('backend/project/index.html.twig', [
            'projects' => $projects,
            'sortBy' => $sortBy,
            'order' => $order,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_backend_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();
            
            $this->addFlash('success', 'Projet créé avec succès !');

            return $this->redirectToRoute('app_backend_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('backend/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_backend_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Projet modifié avec succès !');

            return $this->redirectToRoute('app_backend_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_backend_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
            
            $this->addFlash('success', 'Projet supprimé avec succès !');
        }

        return $this->redirectToRoute('app_backend_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
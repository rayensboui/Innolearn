<?php

namespace App\Controller\Backend;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/export')]
class ExportController extends AbstractController
{
    #[Route('/projects/pdf', name: 'app_backend_project_export_pdf')]
    public function exportProjectsPdf(
        Request $request,
        ProjectRepository $projectRepository
    ): Response
    {
        // Inclure TCPDF manuellement
        $tcpdfPath = $this->getParameter('kernel.project_dir') . '/vendor/tecnickcom/tcpdf/tcpdf.php';
        require_once $tcpdfPath;
        
        // Récupérer les mêmes paramètres que la page
        $sortBy = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');
        $search = $request->query->get('search', '');
        
        // Récupérer les mêmes projets (avec tri et recherche)
        $projects = $projectRepository->findAllWithSortAndSearch($sortBy, $order, $search);
        
        // Créer un nouveau PDF
        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        
        // Information du document
        $pdf->SetCreator('InnoLearn Platform');
        $pdf->SetAuthor('Administrateur InnoLearn');
        $pdf->SetTitle('Export des Projets');
        $pdf->SetSubject('Export PDF des projets InnoLearn');
        
        // Marges
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        
        // Ajouter une page
        $pdf->AddPage();
        
        // Logo et titre
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'InnoLearn - Liste des Projets', 0, 1, 'C');
        
        // Informations de l'export
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Exporté le : ' . date('d/m/Y à H:i'), 0, 1, 'R');
        
        // Filtres appliqués
        $filters = [];
        if ($search) {
            $filters[] = "Recherche : \"$search\"";
        }
        if ($sortBy !== 'id' || $order !== 'asc') {
            $sortText = [
                'id' => 'ID',
                'title' => 'Titre',
                'status' => 'Statut',
                'startDate' => 'Date début',
                'createdAt' => 'Date création'
            ][$sortBy] ?? $sortBy;
            
            $orderText = $order === 'asc' ? 'croissant' : 'décroissant';
            $filters[] = "Tri : $sortText ($orderText)";
        }
        
        if (!empty($filters)) {
            $pdf->Cell(0, 5, 'Filtres appliqués : ' . implode(' | ', $filters), 0, 1, 'L');
        }
        
        $pdf->Ln(10);
        
        // Tableau HTML
        $html = '
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 10px;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
                padding: 8px;
                border: 1px solid #ddd;
                text-align: center;
            }
            td {
                padding: 6px;
                border: 1px solid #ddd;
                font-size: 10px;
            }
            .badge {
                padding: 2px 6px;
                border-radius: 10px;
                font-size: 9px;
                font-weight: bold;
            }
            .badge-draft { background-color: #6c757d; color: white; }
            .badge-active { background-color: #198754; color: white; }
            .badge-completed { background-color: #0dcaf0; color: white; }
            .badge-cancelled { background-color: #dc3545; color: white; }
        </style>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Titre</th>
                    <th width="30%">Description</th>
                    <th width="10%">Statut</th>
                    <th width="10%">Date début</th>
                    <th width="10%">Date fin</th>
                    <th width="15%">Créé le</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($projects as $project) {
            // Badge selon le statut
            $badgeClass = 'badge-' . $project->getStatus();
            $statusText = [
                'draft' => 'Brouillon',
                'active' => 'Actif',
                'completed' => 'Terminé',
                'cancelled' => 'Annulé'
            ][$project->getStatus()] ?? $project->getStatus();
            
            $html .= '
                <tr>
                    <td align="center">' . $project->getId() . '</td>
                    <td>' . htmlspecialchars($project->getTitle()) . '</td>
                    <td>' . htmlspecialchars(substr($project->getDescription(), 0, 100)) . 
                        (strlen($project->getDescription()) > 100 ? '...' : '') . '</td>
                    <td align="center"><span class="badge ' . $badgeClass . '">' . $statusText . '</span></td>
                    <td align="center">' . $project->getStartDate()->format('d/m/Y') . '</td>
                    <td align="center">' . 
                        ($project->getEndDate() ? $project->getEndDate()->format('d/m/Y') : '-') . '</td>
                    <td align="center">' . $project->getCreatedAt()->format('d/m/Y H:i') . '</td>
                </tr>';
        }
        
        // Si aucun projet
        if (empty($projects)) {
            $html .= '
                <tr>
                    <td colspan="7" align="center" style="padding: 20px; color: #666;">
                        Aucun projet ' . ($search ? 'trouvé pour "' . htmlspecialchars($search) . '"' : 'disponible') . '
                    </td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>
        
        <div style="margin-top: 20px; font-size: 9px; color: #666;">
            <strong>Total :</strong> ' . count($projects) . ' projet(s) |
            <strong>Exporté par :</strong> Admin InnoLearn |
            <strong>Page :</strong> {PAGENO}/{nb}
        </div>';
        
        // Écrire le HTML dans le PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Générer le PDF en mémoire
        $pdfContent = $pdf->Output('projets_export.pdf', 'S');
        
        // Créer la réponse HTTP avec le PDF
        $response = new Response($pdfContent);
        
        // Définir les headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'projets_innolearn_' . date('Y-m-d_H-i') . '.pdf'
            )
        );
        $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');
        
        return $response;
    }
}
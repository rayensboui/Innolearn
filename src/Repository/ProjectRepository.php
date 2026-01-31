<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    // Méthode pour tri et recherche
    public function findAllWithSortAndSearch(
        string $sortBy = 'id', 
        string $order = 'asc', 
        string $search = ''
    ): array
    {
        $qb = $this->createQueryBuilder('p');
        
        // Ajouter la recherche si spécifiée
        if (!empty($search)) {
            $qb->where('p.title LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        // Gérer le tri
        $allowedSort = ['id', 'title', 'status', 'startDate', 'createdAt'];
        $allowedOrder = ['asc', 'desc'];
        
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'id';
        $order = in_array($order, $allowedOrder) ? $order : 'asc';
        
        $qb->orderBy('p.' . $sortBy, $order);
        
        return $qb->getQuery()->getResult();
    }
}
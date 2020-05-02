<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @return array<int, Ad>
     */
    public function findBestAds(int $limit = 2): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.comments', 'c')
            ->select('AVG(c.rating) as avgRating, COUNT(c) as sumComments, a as ad')
            ->groupBy('a')
            ->orderBy('avgRating', 'DESC')
            ->having('sumComments > 2')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

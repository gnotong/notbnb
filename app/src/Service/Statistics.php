<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class Statistics
{
    private EntityManagerInterface $manager;

    public function __construct (EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @return array<string, int>
     */
    public function getStats (): array
    {
        $users    = $this->getCountUsers();
        $ads      = $this->getCountAds();
        $bookings = $this->getCountBookings();
        $comments = $this->getCountComments();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountUsers (): string
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountAds (): string
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountBookings (): string
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCountComments (): string
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    /**
     * @param string $order
     * @return array<int, string>
     */
    public function getCountBestAds (string $order): array
    {
        return $this->manager->createQuery("
            SELECT 
                AVG(c.rating) as rating,
                COUNT(c) as comments,
                a.title,
                a.id,
                u.firstName,
                u.lastName,
                u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.author u
            GROUP BY a
            HAVING COUNT(comments) > 1
            ORDER BY rating {$order}
        ")->setMaxResults(5)
        ->getResult();
    }
}
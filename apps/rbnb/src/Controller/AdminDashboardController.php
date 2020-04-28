<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\Statistics;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index (Statistics $stats): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats'   => $stats->getStats(),
            'bestAds' => $stats->getCountBestAds('DESC'),
            'worstAds' => $stats->getCountBestAds('ASC'),
        ]);
    }
}

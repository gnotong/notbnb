<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnounceType;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index (int $page, Paginator $paginator): Response
    {
        $paginator->setEntityClass(Ad::class)
            ->setCurrentPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/admin/{slug}/ads", name="admin_ads_edit")
     */
    public function edit (Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AnnounceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'Announcement successfully modified.'
            );

            return $this->redirectToRoute('admin_ads_index');
        }

        return $this->render("admin/ad/edit.html.twig", [
            'ad'   => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}/delete", name="admin_ads_delete")
     */
    public function delete (Ad $ad, EntityManagerInterface $manager): Response
    {
        if ($ad->getBookings()->count() > 0) {
            $this->addFlash(
                'warning',
                "The announcement <strong>{$ad->getTitle()}</strong> cannot be deleted. It has already been booked up."
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Announcement <strong>{$ad->getTitle()}</strong> successfully deleted."
            );
        }

        return $this->redirectToRoute('admin_ads_index');
    }
}

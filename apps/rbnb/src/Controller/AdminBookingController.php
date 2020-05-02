<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index (int $page, Paginator $paginator): Response
    {
        $paginator->setEntityClass(Booking::class)
            ->setCurrentPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     */
    public function edit (Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Amount sets to 0, because, on preUpdate, the new amount will be updated base on the
            // selected announce
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();
            $this->addFlash(
                'success',
                "The {$booking->getBooker()}'s booking is now up to date."
            );
            return $this->redirectToRoute('admin_bookings_index');
        }

        return $this->render('admin/booking/edit.html.twig', [
            'form'    => $form->createView(),
            'booking' => $booking,
        ]);

    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     */
    public function delete (Booking $booking, EntityManagerInterface $manager): Response
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            'The booking has been successfully deleted'
        );

        return $this->redirectToRoute('admin_bookings_index');
    }
}

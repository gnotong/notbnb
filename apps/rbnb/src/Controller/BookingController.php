<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking)
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * @Route("/ads/{slug}/book", name="booking_ad")
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking
                ->setAd($ad)
                ->setBooker($this->getUser());

            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
        }

        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }

}

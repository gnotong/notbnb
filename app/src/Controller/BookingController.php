<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
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
     */
    public function show (Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form    = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setAd($booking->getAd());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Thank you. Your feedback has been registered !'
            );
        }

        return $this->render('pub/booking/show.html.twig', [
            'booking' => $booking,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/ads/{slug}/book", name="booking_ad")
     * @IsGranted("ROLE_USER")
     */
    public function book (Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $form    = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking
                ->setAd($ad)
                ->setBooker($this->getUser());

            // Check if the dates are available
            if (!$booking->isBookableDates()) {
                $this->addFlash(
                    'warning',
                    'The chosen dates are not available. This accommodation has already been booked for that period'
                );
            } else {
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            }
        }

        return $this->render('pub/booking/book.html.twig', [
            'form' => $form->createView(),
            'ad'   => $ad,
        ]);
    }
}

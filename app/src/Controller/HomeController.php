<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(UserRepository $userRepository, AdRepository $adRepository): Response
    {
        return $this->render('pub/home.html.twig', [
            'bestUsers' => $userRepository->findBestUsers(3),
            'bestAds'   => $adRepository->findBestAds(3),
        ]);
    }
}
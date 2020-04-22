<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_list")
     * @param AdRepository $adRepository
     * @return Response
     */
    public function index(AdRepository $adRepository)
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ads/{slug}", name="ads_show")
     * @param string $slug
     * @param AdRepository $adRepository
     * @return Response
     */
    public function show(string $slug, AdRepository $adRepository)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $adRepository->findOneBy(['slug' => $slug]),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdLike;
use App\Entity\User;
use App\Form\AnnounceType;
use App\Repository\AdLikeRepository;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_list")
     */
    public function index (AdRepository $adRepository): Response
    {
        return $this->render('pub/ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     */
    public function create (Request $request, EntityManagerInterface $manager): Response
    {
        $ad   = new Ad();
        $form = $this->createForm(AnnounceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "Ad <strong>{$ad->getTitle()}</strong> successfully added !");

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug(),]);
        }

        return $this->render('pub/ad/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show (Ad $ad): Response
    {
        return $this->render('pub/ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security(
     *     "is_granted('ROLE_USER') and user === ad.getAuthor()",
     *     message="This ad belongs to another. You cannot edit it."
     * )
     */
    public function edit (Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AnnounceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "Ad <strong>{$ad->getTitle()}</strong> successfully updated !");

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug(),]);
        }

        return $this->render('pub/ad/edit.html.twig', ['form' => $form->createView(), 'ad' => $ad]);
    }

    /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     */
    public function delete (Ad $ad, EntityManagerInterface $manager): RedirectResponse
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', "Ad <strong>{$ad->getTitle()}</strong> successfully deleted !");

        return $this->redirectToRoute('ads_list');
    }

    /**
     * @Route("/ads/{id}/like", name="ads_like")
     */
    public function like(Ad $ad, AdLikeRepository $adLikeRepository, EntityManagerInterface $manager): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if($user == null) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_FORBIDDEN ,
                    'message' => 'You are not allowed like ads until you are logged in.'
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        if ($ad->isLikedByUser($user)) {
            $adLike = $adLikeRepository->findOneBy(['user' => $user, 'ad' => $ad]);
            $manager->remove($adLike);
            $manager->flush();

            return new JsonResponse(
                [
                    'code' => Response::HTTP_OK,
                    'message' => 'Likes has been successfully deleted.',
                    'likes' => $adLikeRepository->count(['ad' => $ad]),
                ],
                Response::HTTP_OK
            );
        }

        $adLike = new AdLike();
        $adLike->setAd($ad)
            ->setUser($user);

        $manager->persist($adLike);
        $manager->flush();

        return new JsonResponse(
            [
                'code' => Response::HTTP_OK,
                'message' => 'Likes has been successfully added.',
                'likes' => $adLikeRepository->count(['ad' => $ad]),
            ],
            Response::HTTP_OK
        );
    }
}

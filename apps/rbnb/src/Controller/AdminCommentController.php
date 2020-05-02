<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index (int $page, Paginator $paginator): Response
    {
        $paginator->setEntityClass(Comment::class)
            ->setCurrentPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     */
    public function edit (Comment $comment, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Comment successfully updated.'
            );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     */
    public function delete (Comment $comment, EntityManagerInterface $manager): Response
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Comment successfully deleted"
        );

        return $this->redirectToRoute('admin_comments_index');
    }
}

<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login (AuthenticationUtils $utils): Response
    {
        $error    = $utils->getLastAuthenticationError();
        $userName = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError'     => $error != null,
            'error'        => $error != null ? $error->getMessage() : '',
            'lastUsername' => $userName,
        ]);
    }

    /**
     * Allows user to logout.
     * Symfony handles it automatically using configurations in security.yml > firewall:admin:logout
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout (): void
    {
    }
}

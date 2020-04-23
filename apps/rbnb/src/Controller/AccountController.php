<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * Allows user to login.
     * Symfony handles it automatically using configurations in security.yml > firewall:main:provider/form_login
     * @Route("/login", name="account_login")
     */
    public function login()
    {
        return $this->render('account/login.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    /**
     * Allows user to logout.
     * Symfony handles it automatically using configurations in security.yml > firewall:main:logout
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
    }
}

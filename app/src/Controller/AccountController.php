<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordResetType;
use App\Form\RegistrationType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Allows user to login.
     * Symfony handles it automatically using configurations in security.yml > firewall:main:provider/form_login
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error    = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('pub/account/login.html.twig', [
            'hasError'     => $error != null,
            'error'        => $error != null ? $error->getMessage() : '',
            'lastUsername' => $username,
        ]);
    }

    /**
     * Allows user to logout.
     * Symfony handles it automatically using configurations in security.yml > firewall:main:logout
     * @Route("/logout", name="account_logout")
     */
    public function logout(): void
    {
    }

    /**
     * UserPasswordEncoderInterface is used in order to tell symfony which algorithm to use (security.yml)
     * @Route("/register", name="account_register")
     */
    public function register(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($password);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your account has been successfully created. You can now log in'
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('pub/account/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Your profile has been successfully updated'
            );
        }

        return $this->render('pub/account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/password", name="account_password")
     * @IsGranted("ROLE_USER")
     */
    public function resetPassword(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ): Response
    {
        $passwordReset = new PasswordReset();

        $form = $this->createForm(PasswordResetType::class, $passwordReset);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordReset->getOld(), $user->getHash())) {
                // Adds an error to the form field `old`
                $form->get('old')->addError(new FormError('Old password is not correct'));
            } else {
                $hash = $encoder->encodePassword($user, $passwordReset->getNew());
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Your password have been successfully changed'
                );
            }
        }

        return $this->render('pub/account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/me", name="account_me")
     * @IsGranted("ROLE_USER")
     */
    public function me(): Response
    {
        return $this->render('pub/user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/account/bookings", name="account_bookings")
     */
    public function booking(BookingRepository $bookingRepository): Response
    {
        // we use this, because in the template, we are unable to directly get bookings from the logged in user
        // app.user.bookings
        // todo: use in the template app.user.bookings after the above error is solved
        // todo: Typed property Proxies\__CG__\App\Entity\User::$ must not be accessed before initialization (in __sleep)
        // todo: This can also solve the problem temporary: public function __sleep(){return [];}
        $bookings = $bookingRepository->findBy(['booker' => $this->getUser()]);

        return $this->render('pub/account/bookings.html.twig', ['bookings' => $bookings]);

    }
}

<?php

namespace App\Presentation\Common\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(
        path: '/login',
        name: 'security_login'
    )]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@common/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(
        path: '/logout',
        name: 'app_logout'
    )]
    public function logout(): void
    {
        throw new RuntimeException(
            'This method can be blank - it will be intercepted by the logout key on your firewall'
        );
    }
}

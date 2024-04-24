<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '{_locale}/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            // Récupérer le rôle de l'utilisateur connecté
            $roles = $this->getUser()->getRoles();

            // Rediriger en fonction du rôle
            if (in_array('ROLE_INGENIEUR', $roles, true)) {
                return $this->redirectToRoute('ingenieur_dashborad');
            } elseif (in_array('ROLE_CLIENT', $roles, true)) {
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '{_locale}/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
         return $this->render('home/index.html.twig');
    }


    
}

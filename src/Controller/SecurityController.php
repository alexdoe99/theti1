<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\GameRepository;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,GameRepository $game): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_home');
        // }
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();
            
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('admin');
            } elseif (in_array('ROLE_EMPLOYE', $roles)) {
                return $this->redirectToRoute('app_employe');
            } elseif (in_array('ROLE_USER', $roles)) {
                return $this->redirectToRoute('app_bienvenu');
            }
            // If the user does not have any specific roles or 'app_home' does not cause a redirect back to 'app_login'
            return $this->redirectToRoute('app_home');
        }
       
        

        // get the login error if there is one
        $date = $game->find(1);
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,'date' => $date]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

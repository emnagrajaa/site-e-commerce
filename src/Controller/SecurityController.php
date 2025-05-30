<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect if already logged in (regular user)
        if ($this->getUser()) {
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Controller should be empty - logout handled by Symfony
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/admin/login', name: 'app_admin_login')]
    public function adminLogin(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        try {
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_dashboard');
            }
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->render('security/admin_login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur s\'est produite. Veuillez réessayer.');
            return $this->redirectToRoute('app_admin_login');
        }
    }
    #[Route(path: '/admin/logout', name: 'app_admin_logout')]
    public function adminLogout(): void
    {
        // Controller should be empty - logout handled by Symfony
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }
}
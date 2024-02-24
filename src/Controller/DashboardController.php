<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DashboardController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/', name: 'admin.redirection', methods: ['GET'])]
    public function redirection() : Response
    {

        return $this->redirectToRoute('auth.login');
    }

    #[Route('/dashboard', name: 'admin.dashboard', methods: ['GET'])]
    public function dashboard() : Response
    {

        return $this->render('dashboard/index.html.twig');

    }

    #[Route('/dashboard/users', name: 'admin.dashboard.users', methods: ['GET'])]
    public function users() : Response
    {

        return $this->render('dashboard/users.html.twig');

    }

    #[Route('/dashboard/reports', name: 'admin.dashboard.reports', methods: ['GET'])]
    public function reports() : Response
    {

        return $this->render('dashboard/reports.html.twig');

    }

    #[Route('/dashboard/settings', name: 'admin.dashboard.settings', methods: ['GET'])]
    public function settings() : Response
    {

        return $this->render('dashboard/settings.html.twig');

    }

}
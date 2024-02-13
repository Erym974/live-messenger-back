<?php

namespace App\Controller;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DashboardController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
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

    #[Route('/dashboard/documentations', name: 'admin.dashboard.documentations', methods: ['GET'])]
    public function documentations() : Response
    {
        
        $yaml = file_get_contents(__DIR__ . '/../../config/documentations.yaml');
        $yaml = Yaml::parse($yaml);

        return $this->render('dashboard/documentations.html.twig', [
            'datas' => $yaml
        ]);

    }

    #[Route('/dashboard/reports', name: 'admin.dashboard.reports', methods: ['GET'])]
    public function reports() : Response
    {

        return $this->render('dashboard/reports.html.twig');

    }

}
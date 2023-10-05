<?php

namespace App\Controller;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class DocumentationController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/documentations', name: 'documentations', methods: ['GET'])]
    public function documentations() : Response
    {

        // load yaml file
        
        $yaml = file_get_contents(__DIR__ . '/../../config/documentations.yaml');
        $yaml = Yaml::parse($yaml);


        return $this->render('documentations/index.html.twig', [
            'datas' => $yaml
        ]);

    }

}
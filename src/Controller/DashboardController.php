<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Post;
use App\Entity\User;
use App\Form\JobType;
use App\Form\PostType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('dashboard/index.html.twig', [
            "active" => "home"
        ]);

    }

    #[Route('/dashboard/users', name: 'admin.dashboard.users', methods: ['GET'])]
    public function users() : Response
    {

        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('dashboard/users.html.twig', [
            "users" => $users,
            "active" => "users"
        ]);

    }

    #[Route('/dashboard/users/{id}', name: 'admin.dashboard.user', methods: ['GET'])]
    public function user(User $user) : Response
    {

        return $this->render('dashboard/user_detail.html.twig', [
            "user" => $user,
            "active" => "users"
        ]);

    }

    #[Route('/dashboard/careers', name: 'admin.dashboard.careers', methods: ['GET', 'POST'])]
    public function careers(Request $request) : Response
    {

        $jobs = $this->entityManager->getRepository(Job::class)->findAll();

        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $job = $form->getData();
            $job->setRequirements(array_filter($job->getRequirements()));
            $this->entityManager->persist($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'Job created successfully');
        }

        return $this->render('dashboard/careers.html.twig', [
            "form" => $form->createView(),
            "jobs" => $jobs,
            "active" => "careers"
        ]);

    }

    #[Route('/dashboard/careers/{id}', name: 'admin.dashboard.career', methods: ['GET', 'POST'])]
    public function career(Job $job, Request $request) : Response
    {

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $job = $form->getData();
            $this->entityManager->persist($job);
            $this->entityManager->flush();
            $this->addFlash('success', 'Job created successfully');
        }

        return $this->render('dashboard/career.html.twig', [
            "form" => $form->createView(),
            "job" => $job
        ]);

    }


    #[Route('/dashboard/blog', name: 'admin.dashboard.blog', methods: ['GET'])]
    public function blog(Request $request) : Response
    {

        $posts = $this->entityManager->getRepository(Post::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post = $form->getData();
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Post posted successfully');
        }

        return $this->render('dashboard/blog.html.twig', [
            "posts" => $posts,
            "form" => $form->createView(),
            "active" => "blog"
        ]);

    }

    #[Route('/dashboard/delete/career/{id}', name: 'admin.dashboard.delete.career', methods: ['GET'])]
    public function career_delete(Job $job, Request $request) : Response
    {

        $this->entityManager->remove($job);
        $this->entityManager->flush();
        $this->addFlash('success', 'Job deleted successfully');

        return $this->redirectToRoute('admin.dashboard.careers');

    }


}
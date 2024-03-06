<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\LegalNotice;
use App\Entity\Post;
use App\Entity\User;
use App\Form\JobType;
use App\Form\LegalNoticeType;
use App\Form\PostType;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DashboardController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }


    #[Route('/', name: 'admin.dashboard', methods: ['GET'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function dashboard() : Response
    {
        return $this->render('dashboard/index.html.twig', [
            "active" => "home"
        ]);

    }

    #[Route('/users', name: 'admin.dashboard.users', methods: ['GET'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function users() : Response
    {

        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('dashboard/users.html.twig', [
            "users" => $users,
            "active" => "users"
        ]);

    }

    #[Route('/users/{id}', name: 'admin.dashboard.user', methods: ['GET'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function user(User $user) : Response
    {

        return $this->render('dashboard/user_detail.html.twig', [
            "user" => $user,
            "active" => "users"
        ]);

    }

    #[Route('/careers', name: 'admin.dashboard.careers', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
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

    #[Route('/terms', name: 'admin.dashboard.terms', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function terms(Request $request) : Response
    {

        $locale = $request->query->get('locale', 'fr');

        switch($locale) {
            case 'english':
            case 'en':
                $termLocale = "English";
                $locale = LegalNotice::ENGLISH;
                break;
            default:
                $termLocale = "Français";
                $locale = LegalNotice::FRENCH;
                break;
        }

        $legalNotices = $this->entityManager->getRepository(LegalNotice::class)->findOneBy(['type' => LegalNotice::TERMS, 'locale' => $locale]);

        if(!$legalNotices) {
            $legalNotices = new LegalNotice();
            $legalNotices->setType(LegalNotice::TERMS);
            $legalNotices->setLocale($locale);
        }

        $form = $this->createForm(LegalNoticeType::class, $legalNotices);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $legalNotices = $form->getData();
            $this->entityManager->persist($legalNotices);
            $this->entityManager->flush();
            $this->addFlash('success', 'Legal notice updated successfully');
        }

        return $this->render('dashboard/terms.html.twig', [
            "form" => $form->createView(),
            "legalNotice_locale" => $termLocale,
        ]);
    }

    #[Route('/privacy', name: 'admin.dashboard.privacy', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function privacy(Request $request) : Response
    {
        $locale = $request->query->get('locale', 'fr');

        switch($locale) {
            case 'english':
            case 'en':
                $termLocale = "English";
                $locale = LegalNotice::ENGLISH;
                break;
            default:
                $termLocale = "Français";
                $locale = LegalNotice::FRENCH;
                break;
        }

        $legalNotices = $this->entityManager->getRepository(LegalNotice::class)->findOneBy(['type' => LegalNotice::PRIVACY, 'locale' => $locale]);

        if(!$legalNotices) {
            $legalNotices = new LegalNotice();
            $legalNotices->setType(LegalNotice::PRIVACY);
            $legalNotices->setLocale($locale);
        }

        $form = $this->createForm(LegalNoticeType::class, $legalNotices);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $legalNotices = $form->getData();
            $this->entityManager->persist($legalNotices);
            $this->entityManager->flush();
            $this->addFlash('success', 'Legal notice updated successfully');
        }

        return $this->render('dashboard/privacy.html.twig', [
            "form" => $form->createView(),
            "legalNotice_locale" => $termLocale,
        ]);
    }

    #[Route('/careers/{id}', name: 'admin.dashboard.career', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
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

    #[Route('/blog/posts', name: 'admin.dashboard.blog', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function blog(Request $request,UploadFileService $UploadFileService) : Response
    {

        /** @var User */
        $user = $this->getUser();

        $posts = $this->entityManager->getRepository(Post::class)->findAll();

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $tempFile = $form->get('image')->getData();
            $post = $form->getData();
            
            $fileResponse = $UploadFileService->uploadFile($tempFile, 'posts_upload_directory');

            if($fileResponse->getStatus()) {
                $post->setImage($fileResponse->getFile());
            } else {
                $this->addFlash('error', $fileResponse->getMessage());
                return $this->redirectToRoute('admin.dashboard.blog');
            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Post posted successfully');
        }

        return $this->render('dashboard/blog.html.twig', [
            "posts" => $posts,
            "form" => $form->createView(),
            "active" => "blog",
            "connected_user" => $user
        ]);

    }

    #[Route('/blog/post/{id}', name: 'admin.dashboard.blog.post', methods: ['GET', 'POST'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function post(Post $post, Request $request, UploadFileService $UploadFileService) : Response
    {

        $form = $this->createForm(PostType::class, $post, [
            'image_required' => false,
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $tempFile = $form->get('image')->getData();
            $post = $form->getData();
            
            if($tempFile) {
                $fileResponse = $UploadFileService->uploadFile($tempFile, 'posts_upload_directory');

                if($fileResponse->getStatus()) {
                    $post->setImage($fileResponse->getFile());
                } else {
                    $this->addFlash('error', $fileResponse->getMessage());
                    return $this->redirectToRoute('admin.dashboard.blog');
                }
            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Post posted successfully');
        }

        return $this->render('dashboard/post.html.twig', [
            "post" => $post,
            "form" => $form->createView(),
            "active" => "blog"
        ]);

    }

    #[Route('/delete/post/{id}', name: 'admin.dashboard.delete.post', methods: ['GET'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function post_delete(Post $post) : Response
    {

        $this->entityManager->remove($post);
        $this->entityManager->flush();
        $this->addFlash('success', 'Post deleted successfully');

        return $this->redirectToRoute('admin.dashboard.blog');

    }

    #[Route('/delete/career/{id}', name: 'admin.dashboard.delete.career', methods: ['GET'], host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function career_delete(Job $job) : Response
    {

        $this->entityManager->remove($job);
        $this->entityManager->flush();
        $this->addFlash('success', 'Job deleted successfully');

        return $this->redirectToRoute('admin.dashboard.careers');

    }

}
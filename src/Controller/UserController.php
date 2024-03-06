<?php

namespace App\Controller;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_MODERATOR')]
class UserController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * 
     * Page du compte utilisateur
     * 
     */
    #[Route('/user/my-account', name: 'user.my-account')]
    public function myAccount(Request $request): Response
    {
        if($request->getMethod() == "POST"){
            $type = $request->request->get('form');

            /** @var User */
            $user = $this->getUser();

            switch($type){
                case 'profile-picture':
                    // $file = $request->files->get('profile-picture');

                    // $oldMedia = $user->getProfilePicture();
                    // if($oldMedia)$this->em->remove($oldMedia);

                    // $media = new Media();
                    // $media->setMediaFile($file);
                    // $user->setProfilePicture($media);
                    
                    // $this->em->persist($media);
                    // $this->em->persist($user);
                    // $this->em->flush();

                    // $this->addFlash('success', 'Your profile picture has been edit');
                    return $this->redirectToRoute('user.my-account');
                break;
            }
            $this->addFlash('success', 'Votre profil a bien été mis à jour');
        }

        return $this->render('dashboard/my-account.html.twig');
    }

}

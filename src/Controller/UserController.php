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

    /**
     * 
     * Page du compte utilisateur
     * 
     */
    #[Route('/user/my-account', name: 'user.my-account', host: 'dashboard.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function myAccount(): Response
    {
        return $this->render('dashboard/my-account.html.twig');
    }

}

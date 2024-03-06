<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_MODERATOR')]
class LanguageController extends AbstractController
{
    #[Route('/language/{locale}', name: 'app_language')]
    public function index(string $locale, Request $request): Response
    {

        $request->getSession()->set('_locale', $locale);

        $redirectTo = "/";
        if($request->headers->get('referer') != ""){
            $redirectTo = $request->headers->get('referer');
        }

        return $this->redirect($redirectTo);
    }
}

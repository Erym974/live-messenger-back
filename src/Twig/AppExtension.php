<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class AppExtension extends AbstractExtension
{

	public function __construct(
		protected RequestStack $requestStack
	)
	{
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('aside_class', [$this, 'getAsideClass'])
		];
	}

	public function getAsideClass()
	{
		$request = $this->requestStack->getCurrentRequest();
		$cookies = $request->cookies;

		if ($cookies->has('asideClass')) return $cookies->get('asideClass');
		return "";
	}

}
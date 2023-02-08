<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry
    ) {}

    #[Route('/login/facebook', name: 'login_facebook')]
    public function loginAction(): RedirectResponse
    {
        return $this->clientRegistry->getClient('facebook')->redirect([], []);
    }

    #[Route('/login/facebook/check', name: 'login_facebook_check')]
    public function checkAction() {}
}
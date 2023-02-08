<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry
    ) {}

    #[Route('/login/google', name: 'login_google')]
    public function loginAction(): RedirectResponse
    {
        return $this->clientRegistry->getClient('google')->redirect([], []);
    }

    #[Route('/login/google/check', name: 'login_google_check')]
    public function checkAction() {}
}
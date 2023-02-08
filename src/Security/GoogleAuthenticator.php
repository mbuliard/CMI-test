<?php

namespace App\Security;

use App\Entity\Member;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GoogleAuthenticator extends AbstractSSOAuthenticator
{
    protected function getCheckRoute(): string
    {
        return 'login_google_check';
    }

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('google');
    }

    protected function getSsoField(): string
    {
        return 'googleId';
    }

    protected function setSsoField(Member $member, string $ssoId): void
    {
        $member->setGoogleId($ssoId);
    }

    protected function generateUsername(ResourceOwnerInterface $ssoUser): string
    {
        return $ssoUser->getFirstName().' '.$ssoUser->getLastName();
    }
}
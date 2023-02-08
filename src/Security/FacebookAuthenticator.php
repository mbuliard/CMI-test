<?php

namespace App\Security;

use App\Entity\Member;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class FacebookAuthenticator extends AbstractSSOAuthenticator
{
    protected function getCheckRoute(): string
    {
        return 'login_facebook_check';
    }

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('facebook');
    }

    protected function getSsoField(): string
    {
        return 'facebookId';
    }

    protected function setSsoField(Member $member, string $ssoId): void
    {
        $member->setFacebookId($ssoId);
    }

    protected function generateUsername(ResourceOwnerInterface $ssoUser): string
    {
        return $ssoUser->getFirstName().' '.$ssoUser->getLastName();
    }
}
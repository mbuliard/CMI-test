<?php

namespace App\Security;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

abstract class AbstractSSOAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        protected readonly ClientRegistry $clientRegistry,
        protected readonly MemberRepository $memberRepository,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly AuthenticationSuccessHandler $authenticationSuccessHandler
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === $this->getCheckRoute();
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->getClient();
        $accessToken = $client->getAccessToken();

        return new SelfValidatingPassport(
            new UserBadge(
                $accessToken->getToken(),
                function () use ($accessToken, $client) {
                    return $this->getOrCreateMember(
                        $client->fetchUserFromToken($accessToken)
                    );
                }
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->authenticationSuccessHandler->handleAuthenticationSuccess($token->getUser());
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }

    abstract protected function getCheckRoute(): string;

    abstract protected function getClient(): OAuth2ClientInterface;

    abstract protected function getSsoField(): string;

    abstract protected function setSsoField(Member $member, string $ssoId): void;

    abstract protected function generateUsername(ResourceOwnerInterface $ssoUser): string;

    private function getOrCreateMember(ResourceOwnerInterface $ssoUser): Member
    {
        $member = $this->memberRepository->findOneBy([$this->getSsoField() => $ssoUser->getId()]);
        if (is_null($member)) {
            $member = $this->createMember($this->generateUsername($ssoUser), $ssoUser->getId());
        }

        return $member;
    }

    private function createMember(string $username, string $ssoId): Member
    {
        $member = new Member($username);
        $this->setSsoField($member, $ssoId);
        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }
}
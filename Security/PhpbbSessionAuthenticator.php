<?php

namespace TeLiXj\PhpbbBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * @author TeLiXj <telixj@gmail.com>
 */
class PhpbbSessionAuthenticator extends AbstractAuthenticator
{
    public const ANONYMOUS_USER_ID = 1;

    public function __construct(private string $cookieName, private string $loginPage, private string $forceLogin, private PhpbbUserProvider $userProvider)
    {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = [
            'ip' => $request->getClientIp(),
            'key' => md5($request->cookies->get($this->cookieName.'_k')),
            'session' => $request->cookies->get($this->cookieName.'_sid'),
            'user' => $request->cookies->get($this->cookieName.'_u'),
        ];

        return new SelfValidatingPassport(new UserBadge($credentials['user'] ?: self::ANONYMOUS_USER_ID, function ($user) use ($credentials) {
            if (!$user || self::ANONYMOUS_USER_ID == $user) {
                return null;
            }
            if ($credentials['session'] && ($user = $this->userProvider->getUserFromSession($credentials['ip'], $credentials['session'], $credentials['user']))) {
                return $user;
            }
            if ($credentials['key'] && $this->userProvider->checkKey($credentials['ip'], $credentials['key'], $credentials['user'])) {
                $this->forceLogin = true;
            }

            return null;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        return $this->forceLogin ? new RedirectResponse($this->loginPage.'&redirect='.$request->getRequestUri()) : null;
    }
}

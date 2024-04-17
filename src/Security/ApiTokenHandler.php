<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(private ApiTokenRepository $apiTokenRepository)
    {

    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $accessToken]);

        if(!$token){
            throw new BadCredentialsException();
        }
       #No tnego la funcion is valid if (!$token->isValid()) {
    #    throw new BadCredentialsException('Token no válido');
    #}
        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }

}
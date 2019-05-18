<?php

namespace App\Security\Authenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Firebase\JWT\JWT;

class LineAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
    }
    
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_line_callback';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getLineClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $lineUser = $this->getLineClient()
                ->fetchUserFromToken($credentials);
        
        $id_token = $credentials->getValues()["id_token"];
        $tokens = explode(".", $id_token);
        
        $lineUserInfo = JWT::urlsafeB64Decode($tokens[1]);
        $email = json_decode($lineUserInfo)->email;
        
        $existingUser = $this->em->getRepository(User::class)
                ->findOneBy(['line_id' => $lineUser->getId()]);
        
        if($existingUser) {
            return $existingUser;
        }
        
        $user = $this->em->getRepository(User::class)
                ->findOneBy(['email' => $email]);
        
        if (!$user) {
            $user = new User();
            $user->setEmail($email);
        }
        
        $user->setLineId($lineUser->getId());
        $this->em->persist($user);
        $this->em->flush();
        
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('login', Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function supportsRememberMe()
    {
        return true;
    }
    
    public function getLineClient()
    {
        return $this->clientRegistry->getClient('line');
    }
}

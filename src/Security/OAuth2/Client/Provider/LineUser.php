<?php

namespace App\Security\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Firebase\JWT\JWT;

class LineUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;
    
    /**
     * @var AccessToken
     */
    protected $token;

    /**
     * @param array $response
     */
    public function __construct(array $response, AccessToken $token)
    {
        $this->response = $response;
        $this->token = $token;
    }

    public function getId()
    {
        return $this->response['userId'];
    }

    /**
     * Get perferred display name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->response['displayName'];
    }

    public function getEmail()
    {
        $id_token = $this->token->getValues()["id_token"];
        $tokens = explode(".", $id_token);
        
        $payload = JWT::urlsafeB64Decode($tokens[1]);
        return isset(json_decode($payload)->email) ? json_decode($payload)->email : null;
    }

    /**
     * Get avatar image URL.
     *
     * @return string|null
     */
    public function getAvatar()
    {
        if (!empty($this->response['pictureUrl'])) {
            return $this->response['pictureUrl'];
        }
    }

    /**
     * Get perferred statusMessage.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->response['statusMessage'];
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}

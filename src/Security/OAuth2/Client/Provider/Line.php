<?php

namespace App\Security\OAuth2\Client\Provider;

use Osapon\OAuth2\Client\Provider\Line as BaseLine;

/**
 * Description of Line
 *
 * @author panpaci
 */
class Line extends BaseLine {
    
    public function getBaseAuthorizationUrl()
    {
        return 'https://access.line.me/oauth2/v2.1/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.line.me/oauth2/v2.1/token';
    }
}

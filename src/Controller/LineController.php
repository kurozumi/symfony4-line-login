<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class LineController extends AbstractController
{
    /**
     * @Route("/connect/line", name="connect_line_start")
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
                ->getClient("line")
                ->redirect();
    }
    
    /**
     * @Route("/connect/line/callback", name="connect_line_callback")
     */
    public function callback(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->json(["User" => (array)$this->getUser()], 200);
    }
}

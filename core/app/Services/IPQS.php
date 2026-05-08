<?php

namespace App\Services;

use GuzzleHttp\Client;

class IPQS
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://ipqualityscore.com/api/json/email/5qHb1r1pHWSRp2p7LXVZ5j8GWCGvXyuE/',
        ]);
    }

    public function checkEmail(string $email)
    {
        $response = $this->client->get("$email");

        $result = json_decode($response->getBody(), true);

        return $result['disposable'] === 'true';
    }
}

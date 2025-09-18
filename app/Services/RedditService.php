<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RedditService
{
    private $client;
    private $clientId;
    private $clientSecret;
    private $userAgent;

    public function __construct()
    {
        // TODO cURL error 60: SSL peer certificate or SSH remote key was not OK (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://www.reddit.com/api/v1/access_token
        $this->client = new Client([
            'verify' => false
        ]);
        $this->clientId = config('services.reddit.client_id');
        $this->clientSecret = config('services.reddit.client_secret');
        $this->userAgent = config('services.reddit.user_agent');
    }

    public function getAccessToken()
    {
        try {
            $response = $this->client->post('https://www.reddit.com/api/v1/access_token', [
                'headers' => [
                    'User-Agent' => $this->userAgent,
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials'
                ]
            ]);

            return json_decode($response->getBody(), true)['access_token'] ?? null;
        }
        catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }

    public function getSubredditPosts(string $token)
    {
        try {
            $response = $this->client->get('https://oauth.reddit.com/r/FridgeDetective/top', [
                'headers' => [
                    'User-Agent' => $this->userAgent,
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            return $response->getBody();
        }
        catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}

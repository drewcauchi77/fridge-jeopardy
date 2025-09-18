<?php

namespace App\Services\Reddit;

use App\Trait\RedditClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;

class OAuthService
{
    use RedditClient;

    public function __construct()
    {
        $this->initialiseRedditClient();
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
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

            return json_decode($response->getBody(), true)['access_token'] ?? '';
        }
        catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
}

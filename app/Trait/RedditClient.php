<?php

namespace App\Trait;

use GuzzleHttp\Client;

trait RedditClient
{
    protected Client $client;
    protected string $clientId;
    protected string $clientSecret;
    protected string $userAgent;

    /**
     * @return void
     */
    protected function initialiseRedditClient(): void
    {
        // TODO cURL error 60: SSL peer certificate or SSH remote key was not OK (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://www.reddit.com/api/v1/access_token
        $this->client = new Client([
            'verify' => false
        ]);

        $this->clientId = config('services.reddit.client_id');
        $this->clientSecret = config('services.reddit.client_secret');
        $this->userAgent = config('services.reddit.user_agent');
    }
}

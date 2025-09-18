<?php

namespace App\Console\Commands;

use App\Services\RedditService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class FetchRedditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-reddit-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(RedditService $redditService): void
    {
        try {
            $this->info("Fetching Reddit Access Token...");
            $token = $redditService->getAccessToken();
            $this->info("Valid Reddit Access Token Obtained...");
            $this->info("Token: ". $token);

            $this->info("Fetching Subreddit Posts...");
            $posts = $redditService->getSubredditPosts($token);
            $this->info("Subreddit Posts Obtained...");
            $this->info("Posts: ". $posts);
        }
        catch (GuzzleException $e) {
            $this->error($e->getMessage());
        }
    }
}

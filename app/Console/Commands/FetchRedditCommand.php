<?php

namespace App\Console\Commands;

use App\Models\Fridge;
use App\Services\Reddit\OAuthService;
use App\Services\Reddit\PostsService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FetchRedditCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fetch:fridges';

    /**
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param OAuthService $redditService
     * @param PostsService $postsService
     * @return void
     */
    public function handle(OAuthService $redditService, PostsService $postsService): void
    {
        try {
            $this->info("Fetching Reddit access token...");
            $token = $redditService->getAccessToken();
            $this->info("Valid Reddit access token obtained...");
            $this->info("Token: ". $token);

            $this->info("Fetching subreddit posts...");
            $posts = $postsService->getPosts($token);
            $this->info("Subreddit posts obtained...");

            $this->info("Processing posts and creating fridge records...");
            $results = $postsService->processPosts($posts);
            $this->displayPostProcessorResults($results);
            $this->info("Done processing posts...");
        }
        catch (GuzzleException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param Collection<int, array{
     *      success: bool,
     *      fridge?: Fridge,
     *      error?: string,
     *      post_data: array{
     *           author: string,
     *           permalink: string,
     *           post_created_at: float
     *      }
     *  }> $results
     * @return void
     */
    private function displayPostProcessorResults(Collection $results): void
    {
        $successful = $results->where('success', true);
        $failed = $results->where('success', false);

        if ($failed->isNotEmpty()) {
            foreach ($failed as $failure) {
                $author = $failure['post_data']['author'];
                $error = $failure['error'] ?? 'Unknown';
                $this->warn("  - {$author}: {$error}");
            }
        }

        foreach ($successful as $success) {
            $fridge = $success['fridge'] ?? null;

            if ($fridge) {
                $this->info("Created fridge: {$fridge->id} by {$fridge->author}");
            }
        }
    }
}

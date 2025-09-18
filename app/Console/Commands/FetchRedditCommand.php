<?php

namespace App\Console\Commands;

use App\Actions\CreateFridgeAction;
use App\Services\RedditService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    public function handle(RedditService $redditService, CreateFridgeAction $createFridge): void
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

            $jsonPosts = json_decode($posts, true);

            foreach ($jsonPosts['data']['children'] as $post) {
                $attributes = [
                    'author' => $post['data']['author'] ?? '',
                    'permalink' => 'https://www.reddit.com' . ($post['data']['permalink'] ?? ''),
                    'post_created_at' => $post['data']['created_utc'] ?? 0,
                ];

                try {
                    // Validate data using the same rules as StoreFridgeRequest
                    $validator = Validator::make($attributes, [
                        'author' => ['required', 'string', 'max:255'],
                        'permalink' => ['required', 'string', 'max:255', 'unique:fridges,permalink'],
                        'post_created_at' => ['required', 'numeric'],
                    ]);

                    if ($validator->fails()) {
                        $this->warn("Skipping invalid post: " . $validator->errors()->first());
                        continue;
                    }

                    // Call the action directly with validated data
                    $fridge = $createFridge->handle($validator->validated());
                    $this->info("Created fridge: {$fridge->id} by {$fridge->author}");
                } catch (\Exception $e) {
                    $this->error("Failed to create fridge: " . $e->getMessage());
                }
            }
        }
        catch (GuzzleException $e) {
            $this->error($e->getMessage());
        }
    }
}

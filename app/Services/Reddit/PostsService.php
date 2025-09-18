<?php

namespace App\Services\Reddit;

use App\Actions\CreateFridgeAction;
use App\Http\Requests\StoreFridgeRequest;
use App\Models\Fridge;
use App\Trait\RedditClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

final class PostsService
{
    use RedditClient;

    /**
     * @param CreateFridgeAction $createFridge
     */
    public function __construct(
        private readonly CreateFridgeAction $createFridge
    ) {
        $this->initialiseRedditClient();
    }

    /**
     * @param string $token
     * @return string
     */
    public function getPosts(string $token): string
    {
        try {
            $response = $this->client->get('https://oauth.reddit.com/r/FridgeDetective/top?raw_json=1', [
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

    /**
     * @param string $postsJson
     * @return Collection<int, array{
     *     success: bool,
     *     fridge?: Fridge,
     *     error?: string,
     *     post_data: array{
     *          author: string,
     *          permalink: string,
     *          post_created_at: float
     *     }
     * }>
     */
    public function processPosts(string $postsJson): Collection
    {
        $posts = json_decode($postsJson, true);
        $results = collect();

        if (!isset($posts['data']['children'])) {
            return $results;
        }

        foreach ($posts['data']['children'] as $post) {
            $result = $this->processSinglePost($post);
            $results->push($result);
        }

        return $results;
    }

    /**
     * @param array<string,mixed> $post
     * @return array{
     *      success: bool,
     *      fridge?: Fridge,
     *      error?: string,
     *      post_data: array{
     *           author: string,
     *           permalink: string,
     *           post_created_at: float
     *      }
     *  }
     */
    private function processSinglePost(array $post): array
    {
        $attributes = $this->getPostAttributes($post);

        try {
            $validator = Validator::make($attributes, $this->getValidationRules());

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'error' => $validator->errors()->first(),
                    'post_data' => $attributes,
                ];
            }

            $fridge = $this->createFridge->handle($validator->validated());

            return [
                'success' => true,
                'fridge' => $fridge,
                'post_data' => $attributes,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'post_data' => $attributes,
            ];
        }
    }

    /**
     * @param array<string,mixed> $post
     * @return array{
     *      author: string,
     *      permalink: string,
     *      post_created_at: float
     * }
     */
    private function getPostAttributes(array $post): array
    {
        return [
            'author' => $post['data']['author'] ?? '',
            'permalink' => 'https://www.reddit.com' . ($post['data']['permalink'] ?? ''),
            'post_created_at' => $post['data']['created_utc'] ?? 0,
        ];
    }

    /**
     * @return array<string, ValidationRule|array<string>|string>
     */
    private function getValidationRules(): array
    {
        $request = new StoreFridgeRequest();
        return $request->rules();
    }
}

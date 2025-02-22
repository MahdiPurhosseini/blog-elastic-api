<?php

namespace App\Repositories;

use App\Interfaces\PostInterface;
use App\Models\Post;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PostRepository implements PostInterface
{
    protected Client $elasticsearch;
    protected string $index = 'posts';

    public function __construct()
    {
        $this->elasticsearch = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    /**
     * Get all posts from both database and Elasticsearch
     */
    public function index(): Collection
    {
        try {
            $results = $this->elasticsearch->search([
                'index' => $this->index,
                'body' => [
                    'query' => [
                        'match_all' => new \stdClass()
                    ]
                ]
            ]);

            return collect($results['hits']['hits'])->map(fn($hit) => $hit['_source']);
        } catch (Exception $e) {
            // Fallback to database if Elasticsearch fails
            return Post::all();
        }
    }

    /**
     * Show specific post with enhanced search
     */
    public function show(Post $post)
    {
        try {
            $result = $this->elasticsearch->get([
                'index' => $this->index,
                'id' => $post->id
            ]);

            return $result['_source'];
        } catch (Exception $e) {
            return $post;
        }
    }

    /**
     * Search posts by query
     */
    public function search(string $query): Collection
    {
        $results = $this->elasticsearch->search([
            'index' => $this->index,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title^3', 'category^2', 'body'],
                        'fuzziness' => 'AUTO'
                    ]
                ]
            ]
        ]);

        return collect($results['hits']['hits'])->map(fn($hit) => $hit['_source']);
    }

    /**
     * Store post in both database and Elasticsearch
     */
    public function store(array $data): Post
    {
        $post = Post::create($data);

        try {
            $this->elasticsearch->index([
                'index' => $this->index,
                'id' => $post->id,
                'body' => $post->toSearchableArray()
            ]);
        } catch (Exception $e) {
            // Log the error but don't fail the operation
            Log::error('Elasticsearch indexing failed: ' . $e->getMessage());
        }

        return $post;
    }

    /**
     * Update post in both database and Elasticsearch
     */
    public function update(Post $post, array $data): Post
    {
        $post->update($data);

        try {
            $this->elasticsearch->update([
                'index' => $this->index,
                'id' => $post->id,
                'body' => [
                    'doc' => $post->toSearchableArray()
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Elasticsearch update failed: ' . $e->getMessage());
        }

        return $post;
    }

    /**
     * Delete post from both database and Elasticsearch
     */
    public function delete(Post $post): bool
    {
        try {
            $this->elasticsearch->delete([
                'index' => $this->index,
                'id' => $post->id
            ]);
        } catch (Exception $e) {
            Log::error('Elasticsearch deletion failed: ' . $e->getMessage());
        }

        return $post->delete();
    }
}

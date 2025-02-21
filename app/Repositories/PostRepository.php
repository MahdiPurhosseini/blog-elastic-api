<?php

namespace App\Repositories;

use App\Interfaces\PostInterface;
use App\Models\Post;

class PostRepository implements PostInterface
{

    protected $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
    }

    public function index()
    {
        return Post::all();
    }

    public function show(Post $post)
    {
        $query = $post->id;
        $results = $this->elasticsearch->search([
            'index' => 'posts',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['title', 'category', 'body']
                    ]
                ]
            ]
        ]);

        return collect($results['hits']['hits'])->map(fn($hit) => $hit['_source']);
    }

    public function store(array $data)
    {
        $post = Post::create($data);

        $this->elasticsearch->index([
            'index' => 'posts',
            'id'    => $post->id,
            'body'  => $post->toArray()
        ]);

        return $post;
    }

    public function update(array $data)
    {
        $post = Post::create($data);

        $this->elasticsearch->index([
            'index' => 'posts',
            'id'    => $post->id,
            'body'  => $post->toArray()
        ]);

        return $post;
    }

    public function delete(Post $post)
    {
        return Post::all();
    }
}

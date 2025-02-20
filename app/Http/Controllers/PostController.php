<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;

class PostController extends Controller
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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category' => 'required|string',
            'body' => 'required|string',
        ]);

        $post = Post::create($request->all());

        $this->elasticsearch->index([
            'index' => 'posts',
            'id'    => $post->id,
            'body'  => $post->toArray()
        ]);

        return $post;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
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
}

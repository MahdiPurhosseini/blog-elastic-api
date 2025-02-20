<?php

namespace App\Console\Commands;

use App\Models\Post;
use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class ReindexPosts extends Command
{
    protected $signature = 'posts:reindex';
    protected $description = 'Reindex all posts in Elasticsearch';

    public function handle()
    {
        $this->info('Reindexing all posts...');
        $elasticsearch = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

        Post::all()->each(function ($post) use ($elasticsearch) {
            $elasticsearch->index([
                'index' => 'posts',
                'id'    => $post->id,
                'body'  => $post->toArray()
            ]);
        });

        $this->info('Reindexing complete!');
    }
}

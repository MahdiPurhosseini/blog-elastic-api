<?php

namespace App\Console\Commands;

use App\Models\Post;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class SetupElasticsearchIndex extends Command
{
    protected $signature = 'elasticsearch:setup-posts
                          {--refresh : Drop and recreate the index}
                          {--sync : Sync all posts after creating index}';
    protected $description = 'Setup Elasticsearch index for posts with proper mapping';
    protected Client $elasticsearch;
    protected string $index = 'posts';

    public function __construct()
    {
        parent::__construct();
        $this->elasticsearch = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    public function handle()
    {
        if ($this->option('refresh')) {
            $this->deleteIndexIfExists();
        }

        $this->createIndex();
        $this->info('Index mapping created successfully');

        if ($this->option('sync')) {
            $this->syncPosts();
        }
    }

    protected function deleteIndexIfExists()
    {
        if ($this->elasticsearch->indices()->exists(['index' => $this->index])) {
            $this->elasticsearch->indices()->delete(['index' => $this->index]);
            $this->info('Existing index deleted');
        }
    }

    protected function createIndex()
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                    'analysis' => [
                        'analyzer' => [
                            'custom_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'stop', 'snowball']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'custom_analyzer',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword'
                                ],
                                'completion' => [
                                    'type' => 'completion'
                                ]
                            ]
                        ],
                        'category' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'body' => [
                            'type' => 'text',
                            'analyzer' => 'custom_analyzer'
                        ],
                        'created_at' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                        ],
                        'updated_at' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'
                        ]
                    ]
                ]
            ]
        ];

        $this->elasticsearch->indices()->create($params);
    }

    protected function syncPosts()
    {
        $bar = $this->output->createProgressBar(Post::count());
        $bar->start();

        Post::chunk(100, function($posts) use ($bar) {
            foreach ($posts as $post) {
                try {
                    $this->elasticsearch->index([
                        'index' => $this->index,
                        'id' => $post->id,
                        'body' => $post->toSearchableArray()
                    ]);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("Error indexing post {$post->id}: {$e->getMessage()}");
                }
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('All posts have been synced to Elasticsearch');
    }
}

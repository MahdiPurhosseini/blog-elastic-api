<?php

namespace App\Http\Controllers;

use Elasticsearch\ClientBuilder;

class HealthCheckController extends Controller
{
    public function elasticSearch()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
        return $client->cluster()->health();
    }
}

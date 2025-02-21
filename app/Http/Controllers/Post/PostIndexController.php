<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Interfaces\PostInterface;

class PostIndexController extends Controller
{
    public function __invoke()
    {
        return app(PostInterface::class)->index();
    }
}

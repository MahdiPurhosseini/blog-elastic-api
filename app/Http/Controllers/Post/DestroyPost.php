<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Interfaces\PostInterface;
use App\Models\Post;

class DestroyPost extends Controller
{
    public function __invoke(Post $post)
    {
        return app(PostInterface::class)->delete($post);
    }
}

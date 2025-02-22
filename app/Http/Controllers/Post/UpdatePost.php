<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostRequest;
use App\Interfaces\PostInterface;
use App\Models\Post;

class UpdatePost extends Controller
{
    public function __invoke(Post $post, UpdatePostRequest $request)
    {
        return app(PostInterface::class)->update($post, $request->validated());
    }
}

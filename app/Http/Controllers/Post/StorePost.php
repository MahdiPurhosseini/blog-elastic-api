<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Interfaces\PostInterface;

class StorePost extends Controller
{
    public function __invoke(StorePostRequest $request)
    {
        return app(PostInterface::class)->store($request->validated());
    }
}

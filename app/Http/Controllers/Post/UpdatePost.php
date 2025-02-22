<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Interfaces\PostInterface;
use Illuminate\Http\Request;

class UpdatePost extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'category' => 'required|string',
            'body' => 'required|string',
        ]);

        return app(PostInterface::class)->update($request->validated());
    }
}

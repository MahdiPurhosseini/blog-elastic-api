<?php

namespace App\Interfaces;

use App\Models\Post;

interface PostInterface
{
    public function index();

    public function show(Post $post);

    public function store(array $data);

    public function update(Post $post, array $data);

    public function delete(Post $post);
}

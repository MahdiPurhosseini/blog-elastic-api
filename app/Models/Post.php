<?php

namespace App\Models;

use App\Http\Constraints\Searchable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;

    protected $fillable = ['title', 'category', 'body'];
}

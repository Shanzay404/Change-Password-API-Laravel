<?php

namespace App\Models;
use App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function product(){
        return $this->hasMany(Post::class);
    }
}

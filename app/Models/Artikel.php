<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'author', 
        'slug', 
        'content', 
        'category'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category');
    }
}

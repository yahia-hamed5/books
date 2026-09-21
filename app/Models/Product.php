<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'bref',
        'slug',
        'image',
        'images',
        'price',
        'qty',
        'is_active',
        'author_id'
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'qty' => 'integer',
        ];
    }
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class)->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }
}

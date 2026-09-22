<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasUuids;
    protected $fillable = [
        'name',
        'image'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

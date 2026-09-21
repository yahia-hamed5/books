<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'parent_id', 'image'];
    public function parent()
    {
        return $this->belongsTo(self::class, "parent_id");
    }
    public function children()
    {
        return $this->hasMany(self::class, "parent_id");
    }

    public function products(){
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

}

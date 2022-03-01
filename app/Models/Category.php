<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property string $title
 * @property string $description
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'category';

    protected $fillable = [
        'title',
        'description',
    ];

    public function products(){
        return $this->belongsToMany(Product::class, 'category_product');
    }
}

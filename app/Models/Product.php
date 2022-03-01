<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Product
 *
 * @property string $title;
 * @property string $vendor_code
 * @property float $price
 * @property string $description
 */
class Product extends Model{

    use HasFactory, SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'title',
        'vendor_code',
        'price',
        'description'
    ];

    public function categories(){
        return $this->belongsToMany(Category::class, 'category_product');
    }

}

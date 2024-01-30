<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_name',
        'product_id',
        'image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany{
        return $this->hasMany(TagImage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('count');
    }

    public static function userCartOrCreate(int $user_id)
    {
        $cart = self::with(['products.images', 'products.category', 'products.color'])->where('user_id', $user_id)->first();
        if (!$cart) {
            $cart = self::create(['user_id' => $user_id]);
        }
        return $cart;
    }

    public function attachProducts($productList)
    {
        $syncData = [];
        foreach ($productList as $product) {
            $syncData[$product['id']] = ['count' => $product['count']];
        }
        $this->products()->syncWithoutDetaching($syncData);
    }
}

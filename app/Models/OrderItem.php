<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'berat',
        'qty',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

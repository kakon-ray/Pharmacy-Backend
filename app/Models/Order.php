<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'category_id',
        'company_id',
        'medicine_id',
        'order_type',
        'quantity',
        'purchase_price',
        'selling_price',
        'expired_date',
    ];

    use HasFactory;
}

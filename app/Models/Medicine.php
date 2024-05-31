<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;


    protected $fillable = [
        'medicine_name',
        'category',
        'brand_name',
        'purchase_date',
        'price',
        'expired_date',
        'stock',
    ];
}

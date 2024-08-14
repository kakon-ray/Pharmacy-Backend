<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'company_id',
        'medicine_name',
        'purchase_date',
        'purchase_price_pice',
        'purchase_price',
        'selling_price',
        'selling_price_pice',
        'expired_date',
        'stock',
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function company(){
        return $this->belongsTo(MedicineCompany::class,'company_id');
    }

    public function order(){
        return $this->hasMany(Order::class,'medicine_id');
    }


}

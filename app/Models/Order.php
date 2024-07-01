<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;

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


    public function medicine(){
        return $this->belongsTo(Medicine::class,'medicine_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function company(){
        return $this->belongsTo(MedicineCompany::class,'company_id');
    }

}

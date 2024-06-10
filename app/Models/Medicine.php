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
        'price',
        'expired_date',
        'stock',
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function company(){
        return $this->belongsTo(MedicineCompany::class,'company_id');
    }


}

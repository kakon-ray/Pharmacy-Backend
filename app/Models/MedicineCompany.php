<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'company_name',
        'company_slug',
    ];

    public function medicine(){
        return $this->hasMany(Medicine::class,'company_id');
    }
}

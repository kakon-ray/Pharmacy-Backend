<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'category_slug',
    ];

    public function medicine(){
        return $this->hasMany(Medicine::class,'category_id');
    }

    public function order(){
        return $this->hasMany(Order::class,'medicine_id');
    }
}

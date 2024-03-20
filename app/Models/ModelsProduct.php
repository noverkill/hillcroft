<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'cat',
        'name',
        'price_ex_vat',
        'price_inc_vat',
        'stock',
        'short_desc',
    ];
}

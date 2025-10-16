<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'daily_rate',
        'status',
        'photo' // Kita akan tambahkan kolom ini
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

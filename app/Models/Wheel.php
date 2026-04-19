<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wheel extends Model
{
    protected $fillable = [
        'user_id',
        'radius',
        'brand',
        'intent',
        'location',
        'photos',
        'slug',
    ];

    protected $casts = [
        'photos' => 'array', // Автоматически превращает JSON из базы в массив PHP
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

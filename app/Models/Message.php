<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    // Разрешаем запись в эти поля
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'is_read'
    ];

    /**
     * Отправитель сообщения
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Получатель сообщения
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}


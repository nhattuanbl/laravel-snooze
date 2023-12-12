<?php

namespace nhattuanbl\Snooze\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifySnoozeRecipient extends Model
{
    use HasFactory;

    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = null;

    protected $dates = [
        'seen_at',
        'created_at',
    ];
    protected $fillable = [
        'channel',
        'content',
        'seen_at',
        'payload',
    ];
    protected $casts = [
        'payload' => 'array',
        'seen_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(config('snooze.user_model'));
    }

    public function notify()
    {
        return $this->belongsTo(NotifySnooze::class);
    }
}

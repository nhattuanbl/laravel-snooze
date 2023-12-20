<?php

namespace Nhattuanbl\Snooze\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string type
 * @property string overlap
 * @property string channel
 * @property string content
 * @property array payload
 * @property Carbon seen_at
 * @property Carbon created_at
 * @property int user_id
 * @property Model user
 * @property int notify_snooze_id
 * @property NotifySnooze notify
 */
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
        'type',
        'overlap',
        'channel',
        'content',
        'seen_at',
        'payload',
        'user_id',
        'notify_snooze_id',
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

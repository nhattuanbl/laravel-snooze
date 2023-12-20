<?php

namespace Nhattuanbl\Snooze\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string> channels
 * @property string content
 * @property string event
 * @property string overlap
 * @property Carbon snooze_until
 * @property Carbon sent_at
 * @property array<int> receiver
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int notify_snooze_template_id
 * @property NotifySnoozeTemplate template
 * @property NotifySnoozeRecipient recipients
 */
class NotifySnooze extends Model
{
    use HasFactory;

    protected $dates = [
        'sent_at',
        'created_at',
        'updated_at',
        'snooze_until'
    ];
    protected $fillable = [
        'channels',
        'content',
        'event',
        'overlap',
        'snooze_until',
        'sent_at',
        'receiver',
        'notify_snooze_template_id'
    ];
    protected $casts = [
        'channels' => 'array',
        'receiver' => 'array',
        'sent_at' => 'datetime:Y-m-d H:i:s',
        'snooze_until' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function template()
    {
        return $this->belongsTo(NotifySnoozeTemplate::class);
    }

    public function recipients()
    {
        return $this->hasMany(NotifySnoozeRecipient::class);
    }
}

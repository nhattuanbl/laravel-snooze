<?php

namespace nhattuanbl\Snooze\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int notify_snooze_template_id
 * @property string content
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
        'content',
        'unique_key',
        'event',
        'snooze_until',
        'sent_at',
        'receiver',
        'notify_snooze_template_id'
    ];
    protected $casts = [
        'receiver' => 'array',
        'sent_at' => 'datetime:Y-m-d H:i:s',
        'snooze_until' => 'datetime:Y-m-d H:i:s',
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

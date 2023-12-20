<?php

namespace Nhattuanbl\Snooze\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string type
 * @property string context
 * @property array<string> channels
 * @property integer min_snooze_daytime
 * @property integer max_snooze_daytime
 * @property integer min_snooze_nighttime
 * @property integer max_snooze_nighttime
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property NotifySnooze notify
 */
class NotifySnoozeTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'type',
        'channels',
        'context',
    ];
    protected $casts = [
        'channels' => 'array',
        'min_snooze_daytime' => 'integer',
        'max_snooze_daytime' => 'integer',
        'min_snooze_nighttime' => 'integer',
        'max_snooze_nighttime' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function notify()
    {
        return $this->hasMany(NotifySnooze::class);
    }
}

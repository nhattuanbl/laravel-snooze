<?php

namespace nhattuanbl\Snooze\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'is_hidden',
    ];
    protected $casts = [
        'channels' => 'array',
        'is_hidden' => 'boolean',
        'min_snooze_daytime' => 'integer',
        'max_snooze_daytime' => 'integer',
        'min_snooze_nighttime' => 'integer',
        'max_snooze_nighttime' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function snoozes()
    {
        return $this->hasMany(NotifySnooze::class);
    }
}

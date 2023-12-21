<?php

namespace Nhattuanbl\Snooze\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Nhattuanbl\Snooze\Models\NotifySnooze;
use Nhattuanbl\Snooze\Models\NotifySnoozeTemplate;

class NotifySnoozeService
{
    CONST START_NIGHT_TIME = '22:00:00';
    CONST END_NIGHT_TIME = '06:00:00';

    /**
     * @param Model|string|array $overlap
     * @param NotifySnoozeTemplate|string $template content or plan to send
     * @param array<int>|int $receiver user_ids
     * @param array<Notification> $channels
     * @param int|float $until in minutes
     * @return NotifySnooze
     */
    public function send($overlap, $template, $receiver = [], array $channels = [], $until = 0.1): NotifySnooze
    {
        if ($overlap instanceof Model) {
            $overlap = get_class($overlap) . ':' . $overlap->id;
        } elseif (is_array($overlap)) {
            $overlap = implode(':', $overlap);
        }

        if (is_numeric($template)) {
            $template = NotifySnoozeTemplate::findOrFail($template);
        }

        $snooze = NotifySnooze::where('overlap', $overlap)->whereNull('sent_at')->first();
        if ($snooze) {
            NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Modify exist snooze');
            $snooze->updated_at = now();

            if (is_string($template)) {
                $snooze->content = $template;
            } else if (!self::isNightTime() || $template->min_snooze_nighttime !== -1) {
                $max_minute = self::isNightTime() ? $template->max_snooze_nighttime : $template->max_snooze_daytime;
                $created_at = $snooze->created_at;
                if ($created_at->addMinutes($max_minute)->gt($snooze->snooze_until)) {
                    NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze plus: ' . ((self::isNightTime() ? $template->min_snooze_nighttime : $template->min_snooze_daytime) ?? 1) . ' minutes');
                    $snooze->snooze_until = $snooze->snooze_until->addMinutes((self::isNightTime() ? $template->min_snooze_nighttime : $template->min_snooze_daytime) ?? 1);
                }
            }

            $snooze->save();
            return $snooze;
        }

        $snooze = NotifySnooze::newModelInstance([
            'overlap' => $overlap,
            'sent_at' => null,
            'snooze_until' => now()->addMinutes($until),
            'receiver' => is_array($receiver) ? $receiver : [$receiver],
            'content' => is_string($template) ? $template : null
        ]);

        if ($template instanceof Model) {
            $snooze->template()->associate($template);
            if (self::isNightTime()) {
                if ($template->min_snooze_nighttime === -1) {
                    if (now()->lt(Carbon::createFromTimeString(self::END_NIGHT_TIME))) {
                        NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze today');
                        $minuteUntilMorning = now()->diffInMinutes(Carbon::createFromTimeString(self::END_NIGHT_TIME));
                    } else {
                        NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze tomorrow');
                        $minuteUntilMorning = 24 * 60 - now()->diffInMinutes(Carbon::createFromTimeString(self::END_NIGHT_TIME));
                    }
                    NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze until morning plus: ' . $minuteUntilMorning . ' minutes');
                    $snooze->snooze_until = now()->addMinutes($minuteUntilMorning);
                } else {
                    NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze nighttime plus: ' . ($template->min_snooze_nighttime ?? 1) . ' minutes');
                    $snooze->snooze_until = now()->addMinutes($template->min_snooze_nighttime ?? 1);
                }
            } else {
                NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze daytime plus: ' . ($template->min_snooze_daytime ?? 1) . ' minutes');
                $snooze->snooze_until = now()->addMinutes($template->min_snooze_daytime ?? 1);
            }
        } else {
            $snooze->channels = $channels;
        }

        $snooze->save();
        return $snooze;
    }

    /**
     * @param $overlap
     * @param Model|int|string $template content to send
     * @param array<int>|int $receiver user_ids
     * @param array<Notification> $channels
     * @return NotifySnooze
     */
    public function sendNow($overlap, $template, $receiver = [], array $channels = []): NotifySnooze
    {
        $snooze = NotifySnooze::newModelInstance([
            'overlap' => $overlap,
            'sent_at' => null,
            'snooze_until' => now(),
            'receiver' => is_array($receiver) ? $receiver : [$receiver],
            'content' => is_string($template) ? $template : null,
            'notify_snooze_template_id' => is_numeric($template) ? $template : null
        ]);

        if ($template instanceof Model) {
            $snooze->template()->associate($template);
        } else if (!$snooze->notify_snooze_template_id) {
            $snooze->channels = $channels;
        }

        $snooze->save();
        NotifySnoozeService::debug('[NotifySnooze #'.$snooze->id.'] Snooze now');
        Artisan::call('notify:snooze', ['id' => $snooze->id]);

        return $snooze;
    }

    private static function isNightTime(): bool
    {
        return now()->gte(Carbon::createFromTimeString(self::START_NIGHT_TIME)) || now()->lte(Carbon::createFromTimeString(self::END_NIGHT_TIME));
    }

    public static function debug(string $message, array $context = [])
    {
        $logChannels = config('logging.channels');
        if (config('snooze.debug', false)) {
            Log::channel('stderr')->debug($message, $context);
            if (array_key_exists('notify_snooze', $logChannels)) {
                Log::channel('notify_snooze')->debug($message, $context);
            }
        }
    }
}

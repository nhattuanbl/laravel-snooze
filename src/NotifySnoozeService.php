<?php

namespace nhattuanbl\Snooze;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use nhattuanbl\Snooze\Models\NotifySnooze;

class NotifySnoozeService
{
    /**
     * @param Model|string|array $unique_key
     * @param string $event
     * @param Model|int|string $template content to send
     * @param int $until in minutes
     * @param array|int $receiver user_ids
     * @return NotifySnooze
     */
    public function send($unique_key, string $event, $template, int $until = 0, $receiver = []): NotifySnooze
    {
        if ($unique_key instanceof Model) {
            $unique_key = get_class($unique_key) . ':' . $unique_key->id;
        } elseif (is_array($unique_key)) {
            $unique_key = implode(':', $unique_key);
        }

        $record = NotifySnooze::where('unique_key', $unique_key)->where('event', $event)->whereNull('send_at')->first();
        if ($record) {
            $record->touch();
        } else {
            $record = NotifySnooze::create([
                'unique_key' => $unique_key,
                'event' => $event,
                'snooze_until' => now()->addMinutes($until),
                'receiver' => $receiver,
                'notify_snooze_template_id' => is_numeric($template) ? $template : $template->id,
            ]);
        }

        return $record;
    }

    /**
     * @param string $event
     * @param Model|int|string $template content to send
     * @param array|int $receiver user_ids
     * @return NotifySnooze
     */
    public function sendNow(string $event, $template, $receiver = []): NotifySnooze
    {
        $ns = new NotifySnooze([
            'unique_key' => null,
            'event' => $event,
            'snooze_until' => now(),
            'receiver' => is_array($receiver) ? $receiver : [$receiver],
        ]);

        if (is_numeric($template)) {
            $ns->notify_snooze_template_id = $template;
        } else if (is_string($template)) {
            $ns->content = $template;
            $ns->notify_snooze_template_id = null;
        } else {
            $ns->template()->create($template->toArray());
        }

        $ns->save();
        Artisan::call('snooze:notify', ['id' => $ns->id]);
        return $ns;
    }
}

<?php

namespace Nhattuanbl\Snooze\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;
use Nhattuanbl\Snooze\Models\NotifySnooze;

class NotifySnoozeCommand extends Command
{
    protected $signature = 'notify:snooze {id?}';

    protected $description = 'Execute notify snooze';

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $id = $this->argument('id');
        $query = NotifySnooze::query();
        if ($id) {
            $query->where('id', $id);
        } else {
            $query->where('snooze_until', '<=', now())->whereNull('sent_at');
        }

        $flag = null;
        $query->chunk(100, function ($records) use ($id, &$flag) {
            foreach ($records as $snooze) {
                if ($snooze->notify_snooze_template_id) {
                    $channels = $snooze->template->channels;
                } else {
                    $channels = $snooze->channels;
                }

                foreach ($channels as $channel) {
                    try {
                        if (!class_exists($channel) || !($channel instanceof Notification)) {
                            throw new \Exception('[NotifySnooze] Channel not found: ' . $channel . ' for #' . $snooze->id);
                        }

                        $user = config('snooze.user_model');
                        $receivers = $user::whereIn('id', $snooze->receiver)->get();
                        foreach ($receivers as $receiver) {
                            $notification = new $channel($snooze);
                            if ($id) $notification->onConnection('sync');
                            $receiver->notify($notification);
                            $snooze->send_at = now();
                            $snooze->save();
                        }
                    } catch (\Exception $e) {
                        $flag = $e;
                    }
                }
            }
        });

        if ($flag) throw $flag;
    }
}

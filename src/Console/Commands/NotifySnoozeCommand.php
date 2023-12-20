<?php

namespace Nhattuanbl\Snooze\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;
use Nhattuanbl\Snooze\Models\NotifySnooze;
use Nhattuanbl\Snooze\Services\NotifySnoozeService;

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
        $query->chunk(100, function ($records) use (&$flag) {
            foreach ($records as $snooze) {
                NotifySnoozeService::debug('[NotifySnooze] Sending #' . $snooze->id);

                if ($snooze->notify_snooze_template_id) {
                    $channels = $snooze->template->channels;
                } else {
                    $channels = $snooze->channels;
                }

                foreach ($channels as $channel) {
                    try {
                        if (!class_exists($channel)) {
                            throw new \Exception('[NotifySnooze] Channel not found: ' . $channel . ' for #' . $snooze->id);
                        }

                        $notification = new $channel($snooze);
                        if (! $notification instanceof Notification) {
                            throw new \Exception('[NotifySnooze] Bad Notification ' . $channel);
                        }

                        $user = config('snooze.user_model');
                        $user::whereIn('id', $snooze->receiver)->chunk(500, function ($users) use($notification, $snooze) {
                            foreach ($users as $user) {
                                if ($this->argument('id')) {
                                    NotifySnoozeService::debug('[NotifySnooze] Notify now #'.$snooze->id.' on user #' . $user->id);
                                    $user->notifyNow($notification);
                                } else {
                                    NotifySnoozeService::debug('[NotifySnooze] Notify #'.$snooze->id.' on user #' . $user->id);
                                }

                                $user->notify($notification);
                                $snooze->sent_at = now();
                                $snooze->save();
                            }
                        });
                    } catch (\Exception $e) {
                        $flag = $e;
                    }
                }
            }
        });

        if ($flag) {
            NotifySnoozeService::debug('[NotifySnooze] ' . $flag->getMessage());
            throw $flag;
        }
    }
}

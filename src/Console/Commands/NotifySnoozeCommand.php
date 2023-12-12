<?php

namespace nhattuanbl\Snooze\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use nhattuanbl\Snooze\Models\NotifySnooze;

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
            foreach ($records as $record) {
                $channels = $record->template->channels;
                foreach ($channels as $channel) {
                    try {
                        if (class_exists($channel)) {
                            $user = config('snooze.user_model');
                            $receivers = $user::whereIn('id', $record->receiver)->get();
                            foreach ($receivers as $receiver) {
                                $notification = new $channel($record);
                                if ($id) $notification->onConnection('sync');
                                $receiver->notify($notification);
                                $record->send_at = now();
                                $record->save();
                            }
                        } else {
                            throw new \Exception('[NotifySnooze] Channel not found: ' . $channel . ' for #' . $record->id);
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

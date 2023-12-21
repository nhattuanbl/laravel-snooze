<?php

namespace Nhattuanbl\Snooze\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Nhattuanbl\Snooze\Models\NotifySnoozeRecipient;
use Nhattuanbl\Snooze\Notifications\INotifySnooze;

class NotifySnoozeListener
{
    public function handle(NotificationSent $event): void
    {
        /** @var Notification $notification */
        $notification = $event->notification;
        if (!$notification instanceof INotifySnooze) {
            return;
        }

        $user = $event->notifiable;
        $channel = $event->channel;
        $response = $event->response;
        $snooze = $notification->snooze;

        $recipient = NotifySnoozeRecipient::create([
            'channel' => $channel,
            'type' => isset($snooze->template) ? $snooze->template->type : null,
            'overlap' => $snooze->overlap,
            'seen_at' => null,
            'payload' => $notification->payload ?? null,
            'user_id' => $user->id,
            'notify_snooze_id' => $notification->snooze->id,
            'content' => $notification->content ?? null,
        ]);

        $notification->after($recipient, $response);
    }
}

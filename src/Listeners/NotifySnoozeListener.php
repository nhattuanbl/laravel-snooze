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
        $type = $snooze->template ? $snooze->template->type : $snooze->event;

        $recipient = NotifySnoozeRecipient::create([
            'channel' => $channel,
            'type' => $type,
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

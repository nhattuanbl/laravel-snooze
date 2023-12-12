<?php

namespace nhattuanbl\Snooze\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use nhattuanbl\Snooze\Models\NotifySnoozeRecipient;
use nhattuanbl\Snooze\Notifications\INotifySnooze;

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

        $recipient = NotifySnoozeRecipient::create([
            'channel' => $channel,
            'content' => $notification->content,
            'seen_at' => null,
            'payload' => $notification->payload,
            'user_id' => $user->id,
            'notify_snooze_id' => $notification->snooze->id,
        ]);

        if (method_exists($notification, 'after')) {
            $notification->after($recipient, $response);
        }
    }
}

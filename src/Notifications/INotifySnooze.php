<?php

namespace Nhattuanbl\Snooze\Notifications;

use Nhattuanbl\Snooze\Models\NotifySnooze;
use Nhattuanbl\Snooze\Models\NotifySnoozeRecipient;

/**
 * @property NotifySnooze snooze
 * @property array payload
 * @property string content
 */
interface INotifySnooze
{
    public function __construct(NotifySnooze $snooze);
    public function after(NotifySnoozeRecipient $recipient, $response);
}

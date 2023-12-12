<?php

namespace nhattuanbl\Snooze\Notifications;

use nhattuanbl\Snooze\Models\NotifySnooze;
use nhattuanbl\Snooze\Models\NotifySnoozeRecipient;

/**
 * @property NotifySnooze snooze
 * @property array payload
 * @property string content
 * @method after(NotifySnoozeRecipient $recipient, $response)
 */
interface INotifySnooze
{

}



## Installation
```
composer require nhattuanbl/snooze
```

Important: With version 5.4 or below, you must register your service providers manually in the providers section of the `config/app.php` configuration file in your laravel project.

```
'providers' => [
    // Other Service Providers

    Nhattuanbl\Snooze\SnoozeServiceProvider::class,
],

'aliases' => [
    // Other aliases

    'Snooze' => Nhattuanbl\Snooze\Facades\Snooze::class,
],
```

Publish config file
```
php artisan vendor:publish --provider="Nhattuanbl\Snooze\NotifySnoozeProvider" --tag="config"
```
Publish migration file
```
php artisan vendor:publish --provider="Nhattuanbl\Snooze\NotifySnoozeProvider" --tag="migration"
```
```
php artisan migrate
php artisan schedule:work
```

## Usage
### Adding notification
| Snooze::send( |                              |                                                |                                  | 
|---------------|------------------------------|------------------------------------------------|----------------------------------|
| $overlap      | string\|Model                | Unique key of the notification avoid duplicate | App\Models\Comment::first()      |
| $event        | string                       | Type of event                                  | "COMMENT"                        |
| $template     | string\|NotifySnoozeTemplate | Text or NotifySnoozeTemplate Model             | "Someone comment on your post"   |
| $receiver     | array<int>\|int              | Users who will receive the notification        | App\Models\User::first()         |
| $channels     | array<Notification>          | Notification class handling                    | App\Notification\BillMail::class |
| $util         | int                          | Snooze time in minute                          | 1                                |
| );            |                              |                                                |                                  |

Send notification immediately
```
\Nhattuanbl\Snooze\Facades\Snooze::sendNow($overlap, string $event, $template, $receiver, array $channels = [])
```

### Create template
| NotifySnoozeTemplate::create([ |        |        | default                  | 
|--------------------------------|--------|--------|--------------------------|
| type                           | string | unique |                          |
| channels                       | array  |        |                          |
| context                        | string |        |                          |
| min_snooze_daytime             | int    |        | 1                        |
| max_snooze_daytime             | int    |        | 5                        |
| min_snooze_nighttime           | int    |        | -1 //wait until morning  |
| max_snooze_nighttime           | int    |        | 5                        |
| ]);                            |        |        |                          | 

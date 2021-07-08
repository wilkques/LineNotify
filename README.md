# LineNotify

[![Latest Stable Version](https://poser.pugx.org/wilkques/line-notify/v/stable)](https://packagist.org/packages/wilkques/line-notify)
[![License](https://poser.pugx.org/wilkques/line-notify/license)](https://packagist.org/packages/wilkques/line-notify)

````
composer require wilkques/line-notify
````

# GENERATE URL
````php
use Wilkques\LineNotify\Notify;

$notify = new Notify('<CHANNEL_ID>');
$url = $notify->generateSubscribeUrl($options);
````
# GET TOKEN
````php
use Wilkques\LineNotify\Notify;

$notify = new Notify('<CHANNEL_ID>', '<CHANNEL_SECRET>');
$token = $notify->requestToken($_GET('code'));
````

# PUSH MESSAGE
````php
use Wilkques\LineNotify\Notify;
use Wilkques\LineNotify\Message;

$message = new Message('<Notify Text>');
Notify::sendMessage($token, $message);
````

# REFERENCE
[Official](https://notify-bot.line.me/doc/en/)

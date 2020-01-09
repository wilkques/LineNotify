# LineNotify
````
composer require wilkques/line-notify
````

# GENERATE URL
````
use Wilkques\LineNotify\Notify;

$notify = new Notify(CHANNEL_ID);
$url = $notify->generateSubscribeUrl($options);
````
# GET TOKEN
````
use Wilkques\LineNotify\Notify;

$notify = new Notify(CHANNEL_ID,CHANNEL_SECRET);
$token = $notify->requestToken($_GET('code'));
````

# PUSH MESSAGE
````
use Wilkques\LineNotify\Notify;
use Wilkques\LineNotify\Message;

$message = new Message('Notify Text');
Notify::sendMessage($token,$message);
````

# REFERENCE
https://notify-bot.line.me/doc/en/

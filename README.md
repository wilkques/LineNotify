# LineNotify

[![Latest Stable Version](https://poser.pugx.org/wilkques/line-notify/v/stable)](https://packagist.org/packages/wilkques/line-notify)
[![License](https://poser.pugx.org/wilkques/line-notify/license)](https://packagist.org/packages/wilkques/line-notify)

````
composer require wilkques/line-notify
````
# How to use
1. GENERATE URL
    ````php
    use Wilkques\LineNotify\Notify;

    $notify = new Notify('<CHANNEL_ID>');
    $url = $notify->generateSubscribeUrl($options);
    // or
    $url = Notify::clientId('<CHANNEL_ID>')->generateSubscribeUrl($options);
    ````
2. GET TOKEN
    ````php
    use Wilkques\LineNotify\Notify;

    $notify = new Notify('<CHANNEL_ID>', '<CHANNEL_SECRET>');
    $token = $notify->requestToken($_GET('code'))->accessToken();
    // or
    $token = Notify::clientId('<CHANNEL_ID>')
    ->clientSecret('<CHANNEL_SECRET>')
    ->requestToken($_GET('code'))
    ->throw() // throw exception
    ->accessToken();
    ````

3. PUSH MESSAGE
    ````php
    use Wilkques\LineNotify\Notify;
    use Wilkques\LineNotify\Message;

    $message = new Message('<Notify Text>');
    $response = Notify::token($token)->sendMessage($message);

    $response->throw(); // throw exceptions

    $response->throw(function ($response, $exception) {
        // code
    });
    ````
4. Methods
    1. Response see REFERENCE [Http Client](#REFERENCE)
        |   Methods     |   Description    |
        |---------------|------------------|
        `throw`         | throw Exception

# REFERENCE
1. [Official](https://notify-bot.line.me/doc/en/)
1. [Http Client](https://github.com/wilkques/http-client)

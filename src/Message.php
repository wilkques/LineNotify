<?php

namespace Wilkques\LineNotify;

class Message
{
    protected $message = [];

    public function __construct($text)
    {
        $this->message['message'] = $text;
    }

    public function setImageThumbnail($url)
    {
        $this->message['imageThumbnail'] = $url;
        return $this;
    }

    public function setImageFullsize($url)
    {
        $this->message['imageFullsize'] = $url;
        return $this;
    }

    public function setStickerPackageId($packageId)
    {
        $this->message['stickerPackageId'] = $packageId;
        return $this;
    }

    public function setStickerId($stickerId)
    {
        $this->message['stickerId'] = $stickerId;
        return $this;
    }

    public function setNotificationDisabled($boolean = false)
    {
        $this->message['notificationDisabled'] = $boolean;
        return $this;
    }

    public function build()
    {
        return $this->message;
    }
}

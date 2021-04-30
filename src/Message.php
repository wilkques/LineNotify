<?php

namespace Wilkques\LineNotify;

class Message
{
    /** @var array */
    protected $message = [];

    public function __construct(string $text = '')
    {
        $this->setMessage($text);
    }

    /**
     * @param string $message
     * 
     * @return static
     */
    public function setMessage(string $message)
    {
        $this->message['message'] = $message;

        return $this;
    }

    /**
     * @param string $url
     * 
     * @return static
     */
    public function setImageThumbnail(string $url)
    {
        $this->message['imageThumbnail'] = $url;

        return $this;
    }

    /**
     * @param string $url
     * 
     * @return static
     */
    public function setImageFullsize(string $url)
    {
        $this->message['imageFullsize'] = $url;

        return $this;
    }

    /**
     * @param integer $packageId
     * 
     * @return static
     */
    public function setStickerPackageId(int $packageId)
    {
        $this->message['stickerPackageId'] = $packageId;

        return $this;
    }

    /**
     * @param integer $stickerId
     * 
     * @return static
     */
    public function setStickerId(int $stickerId)
    {
        $this->message['stickerId'] = $stickerId;

        return $this;
    }

    /**
     * @param boolean $boolean
     * 
     * @return static
     */
    public function setNotificationDisabled(bool $boolean = false)
    {
        $this->message['notificationDisabled'] = $boolean;

        return $this;
    }

    /**
     * @return array
     */
    public function build()
    {
        return $this->message;
    }
}

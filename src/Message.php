<?php

namespace Wilkques\LineNotify;

/**
 * @method static static options(array $options)
 * @method static static message(string $message)
 * @method static static imageThumbnail(string $imageThumbnail)
 * @method static static imageFullsize(string $imageFullsize)
 * @method static static stickerPackageId(int $stickerPackageId)
 * @method static static stickerId(int $stickerId)
 * @method static static notificationDisabled(boole $notificationDisabled)
 */
class Message
{
    /** @var array */
    protected $options = [];
    /** @var array */
    protected $methods = [
        'options', 'message', 'imageThumbnail', 'imageFullsize', 'stickerPackageId', 'stickerId',
        'notificationDisabled'
    ];

    /**
     * @param string $text
     */
    public function __construct(string $text = '')
    {
        $this->setMessage($text);
    }

    /**
     * @param array $options
     * 
     * @param static
     */
    public function setOptions(array $options)
    {
        $this->options = array_replace_recursive($this->getOptions(), $options);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $message
     * 
     * @return static
     */
    public function setMessage(string $message)
    {
        $this->setOptions(compact('message'));

        return $this;
    }

    /**
     * @param string $imageThumbnail
     * 
     * @return static
     */
    public function setImageThumbnail(string $imageThumbnail)
    {
        $this->setOptions(compact('imageThumbnail'));

        return $this;
    }

    /**
     * @param string $imageFullsize
     * 
     * @return static
     */
    public function setImageFullsize(string $imageFullsize)
    {
        $this->setOptions(compact('imageFullsize'));

        return $this;
    }

    /**
     * @param integer $stickerPackageId
     * 
     * @return static
     */
    public function setStickerPackageId(int $stickerPackageId)
    {
        $this->setOptions(compact('stickerPackageId'));

        return $this;
    }

    /**
     * @param integer $stickerId
     * 
     * @return static
     */
    public function setStickerId(int $stickerId)
    {
        $this->setOptions(compact('stickerId'));

        return $this;
    }

    /**
     * @param boolean $notificationDisabled
     * 
     * @return static
     */
    public function setNotificationDisabled(bool $notificationDisabled = false)
    {
        $this->setOptions(compact('notificationDisabled'));

        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static
     */
    public function __call(string $method, array $arguments)
    {
        $method = ltrim(trim($method));

        if (in_array($method, $this->methods)) {
            $method = 'set' . ucfirst($method);
        }

        return $this->{$method}(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return static
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return (new static)->{$method}(...$arguments);
    }
}

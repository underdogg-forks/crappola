<?php

namespace App\Libraries\Skype;

class SkypeResponse
{
    public $type;
    /**
     * @var never[]|mixed[]
     */
    public $attachments = [];
    public $text;
    public function __construct($type)
    {
        $this->type = $type;
    }

    public static function message($message)
    {
        $instance = new self('message/text');
        $instance->setText($message);

        return json_encode($instance);
    }

    public function setText($text): void
    {
        $this->text = $text;
    }

    public function addAttachment($attachment): void
    {
        $this->attachments[] = $attachment;
    }
}

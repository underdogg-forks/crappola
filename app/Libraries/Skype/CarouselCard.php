<?php

namespace App\Libraries\Skype;

class CarouselCard
{
    /**
     * @var string
     */
    public $contentType = 'application/vnd.microsoft.card.carousel';
    /**
     * @var never[]|mixed[]
     */
    public $attachments = [];
    public function __construct()
    {
    }

    public function addAttachment($attachment): void
    {
        $this->attachments[] = $attachment;
    }
}

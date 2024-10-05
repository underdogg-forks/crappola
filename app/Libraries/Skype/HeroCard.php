<?php

namespace App\Libraries\Skype;

use stdClass;

class HeroCard
{
    /**
     * @var string
     */
    public $contentType = 'application/vnd.microsoft.card.hero';
    /**
     * @var stdClass
     */
    public $content;
    public function __construct()
    {
        $this->content = new stdClass();
        $this->content->buttons = [];
    }

    public function setTitle($title): void
    {
        $this->content->title = $title;
    }

    public function setSubitle($subtitle): void
    {
        $this->content->subtitle = $subtitle;
    }

    public function setText($text): void
    {
        $this->content->text = $text;
    }

    public function addButton($type, $title, $value, $url = false): void
    {
        $this->content->buttons[] = new ButtonCard($type, $title, $value, $url);
    }
}

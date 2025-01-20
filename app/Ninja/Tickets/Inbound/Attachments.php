<?php

namespace App\Ninja\Tickets\Inbound;

use Iterator;

/**
 * Class Attachments.
 */
class Attachments implements Iterator
{
    /**
     * @var Attachments
     */
    protected $attachments;

    /**
     * Attachments constructor.
     *
     * @param bool $attachments
     */
    public function __construct($attachments)
    {
        $this->attachments = $attachments;

        $this->position = 0;
    }

    /**
     * @return Attachment|bool
     */
    public function get($key)
    {
        $this->position = $key;

        if (! empty($this->attachments[$key])) {
            return new Attachment($this->attachments[$key]);
        }

        return false;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @return Attachment
     */
    public function current()
    {
        return new Attachment($this->attachments[$this->position]);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->attachments[$this->position]);
    }
}

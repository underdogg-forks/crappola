<?php

namespace App\Ninja\Presenters;

use Laracasts\Presenter\Presenter;
use stdClass;
use Utils;

class EntityPresenter extends Presenter
{
    /**
     * @return string
     */
    public function url(): string
    {
        return SITE_URL . $this->path();
    }

    public function path(): string
    {
        $type = Utils::pluralizeEntityType($this->entity->getEntityType());
        $id = $this->entity->public_id;

        return sprintf('/%s/%s', $type, $id);
    }

    public function editUrl(): string
    {
        return $this->url() . '/edit';
    }

    public function statusLabel($label = false): string
    {
        $class = '';
        $text = '';
        if ( ! $this->entity->id) {
            return '';
        }

        if ($this->entity->is_deleted) {
            $class = 'danger';
            $label = trans('texts.deleted');
        } elseif ($this->entity->trashed()) {
            $class = 'warning';
            $label = trans('texts.archived');
        } else {
            $class = $this->entity->statusClass();
            $label = $label ?: $this->entity->statusLabel();
        }

        return sprintf('<span style="font-size:13px" class="label label-%s">%s</span>', $class, $label);
    }

    public function statusColor(): string
    {
        $class = $this->entity->statusClass();

        return match ($class) {
            'success' => '#5cb85c',
            'warning' => '#f0ad4e',
            'primary' => '#337ab7',
            'info'    => '#5bc0de',
            default   => '#777',
        };
    }

    /**
     * @return mixed
     */
    public function link()
    {
        $name = $this->entity->getDisplayName();
        $link = $this->url();

        return link_to($link, $name)->toHtml();
    }

    public function titledName(): string
    {
        $entity = $this->entity;
        $entityType = $entity->getEntityType();

        return sprintf('%s: %s', trans('texts.' . $entityType), $entity->getDisplayName());
    }

    public function calendarEvent($subColors = false): stdClass
    {
        $entity = $this->entity;

        $data = new stdClass();
        $data->id = $entity->getEntityType() . ':' . $entity->public_id;
        $data->allDay = true;
        $data->url = $this->url();

        return $data;
    }
}

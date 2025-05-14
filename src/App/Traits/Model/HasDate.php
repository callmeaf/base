<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Enums\DateTimeFormat;

trait HasDate
{
    public function createdAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => $this->created_at ? verta($this->created_at)->format($format->value) : null,
            default => $this->created_at ? $this->created_at->format($format->value) : null,
        };
    }

    public function updatedAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => $this->updated_at ?  verta($this->updated_at)->format($format->value) : null,
            default => $this->updated_at ? $this->updated_at?->format($format->value) : null,
        };
    }

    public function deletedAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => $this->deleted_at ? verta($this->deleted_at)->format($format->value) : null,
            default =>  $this->deleted_at ? $this->deleted_at->format($format->value) : null,
        };
    }
}

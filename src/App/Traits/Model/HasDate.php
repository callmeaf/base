<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Enums\DateTimeFormat;

trait HasDate
{
    public function createdAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => verta($this->created_at)->format($format->value),
            default => $this->created_at?->format($format->value),
        };
    }

    public function updatedAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => verta($this->updated_at)->format($format->value),
            default => $this->updated_at?->format($format->value),
        };
    }

    public function deletedAtText(DateTimeFormat $format = DateTimeFormat::DATE)
    {
        return match (app()->currentLocale()) {
            'fa' => empty($this->deleted_at) ? null : verta($this->deleted_at)->format($format->value),
            default => $this->deleted_at?->format($format->value),
        };
    }
}

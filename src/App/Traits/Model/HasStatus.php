<?php

namespace Callmeaf\Base\App\Traits\Model;

use Callmeaf\Base\App\Enums\BaseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasStatus
{
    public function scopeActive(Builder $query): void
    {
        $query->where('status', BaseStatus::ACTIVE->value);
    }

    public function scopeInActive(Builder $query): void
    {
        $query->where('status', BaseStatus::INACTIVE->value);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('status', BaseStatus::PENDING->value);
    }

    public function scopeOfStatus(Builder $query, mixed $value): void
    {
        $query->where('status', $value);
    }

    public function isActive(): bool
    {
        if ($this->status instanceof \UnitEnum) {
            return $this->status->value === BaseStatus::ACTIVE->value;
        }
        return $this->status === BaseStatus::ACTIVE->value;
    }

    public function isInActive(): bool
    {
        if ($this->status instanceof \UnitEnum) {
            return $this->status->value === BaseStatus::INACTIVE->value;
        }
        return $this->status === BaseStatus::INACTIVE->value;
    }

    public function isPending(): bool
    {
        if ($this->status instanceof \UnitEnum) {
            return $this->status->value === BaseStatus::PENDING->value;
        }
        return $this->status === BaseStatus::PENDING->value;
    }

    public function isStatus(mixed $value): bool
    {
        if ($this->status instanceof \UnitEnum) {
            return $this->status->value === $value;
        }
        return $this->status === $value;
    }

    public function statusText(): Attribute
    {
        return Attribute::get(fn() => \Base::enumCaseTranslator(
            \Base::getPackageNameFromModel(model: self::class),
            $this->status,
        ));
    }
}

<?php

namespace Callmeaf\Base\Utilities\V1;

use Callmeaf\Base\Enums\DateTimeFormat;
use Callmeaf\Base\Utilities\V1\Contracts\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class Searcher implements SearcherInterface
{
    public function apply(Builder $query,array $filters = []): void
    {
        $filters = collect($filters)->filter(fn($item) => strlen(trim($item)));
        if($value = $filters->get('status')) {
            $query->where('status',$value);
        }
        if($value = $filters->get('type')) {
            $query->where('type',$value);
        }
        if($value = $filters->get('status')) {
            $query->where('status',$value);
        }
        if($value = $filters->get('parent_id')) {
            if(strval($value) === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id',$value);
            }
        }
        if($value = $filters->get('created_from')) {
            $query->whereDate('created_at','>=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('created_to')) {
            $query->whereDate('created_at','<=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('updated_from')) {
            $query->whereDate('updated_at','>=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('updated_to')) {
            $query->whereDate('updated_at','<=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('deleted_from')) {
            $query->whereDate('deleted_at','>=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('deleted_to')) {
            $query->whereDate('deleted_at','<=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('published_from')) {
            $query->whereDate('published_at','>=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('published_to')) {
            $query->whereDate('published_at','<=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('expired_from')) {
            $query->whereDate('expired_at','>=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
        if($value = $filters->get('expired_to')) {
            $query->whereDate('expired_at','<=',jalaliToGregorian(date: $value,dateTimeFormat: DateTimeFormat::DATE_TIME_WITH_DASH));
        }
    }
}

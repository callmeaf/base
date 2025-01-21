<?php

namespace Callmeaf\Base\Utilities\V1;

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
            $query->where('created_at','>=',jalaliToGregorian($value));
        }
        if($value = $filters->get('created_to')) {
            $query->where('created_at','<=',jalaliToGregorian($value));
        }
        if($value = $filters->get('updated_from')) {
            $query->where('updated_at','>=',jalaliToGregorian($value));
        }
        if($value = $filters->get('updated_to')) {
            $query->where('updated_at','<=',jalaliToGregorian($value));
        }
        if($value = $filters->get('deleted_from')) {
            $query->where('deleted_at','>=',jalaliToGregorian($value));
        }
        if($value = $filters->get('deleted_to')) {
            $query->where('deleted_at','<=',jalaliToGregorian($value));
        }
        if($value = $filters->get('published_from')) {
            $query->where('published_at','>=',jalaliToGregorian($value));
        }
        if($value = $filters->get('published_to')) {
            $query->where('published_at','<=',jalaliToGregorian($value));
        }
        if($value = $filters->get('expired_from')) {
            $query->where('expired_at','>=',jalaliToGregorian($value));
        }
        if($value = $filters->get('expired_to')) {
            $query->where('expired_at','<=',jalaliToGregorian($value));
        }
    }
}

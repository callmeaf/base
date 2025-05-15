<?php

namespace Callmeaf\Base\App\Models\Contracts;

interface HasMedia extends \Spatie\MediaLibrary\HasMedia
{
    public function mediaCollectionName(): string;
    public function mediaDiskName(): string;
}

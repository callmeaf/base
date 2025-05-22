<?php

namespace Callmeaf\Base\App\Repo\Contracts;

use Callmeaf\Media\App\Models\Media;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaMethodsInterface
{
    /**
     * @param JsonResource|string|int $id
     * @param UploadedFile $file
     * @param string|null $collectionName
     * @param string|null $diskName
     * @param bool $removeOldMedia
     * @return Media
     */
    public function addMedia(JsonResource|string|int $id,UploadedFile $file,?string $collectionName = 'default',?string $diskName = '',bool $removeOldMedia = true);

    /**
     * @param JsonResource|string|int $id
     * @param array $files
     * @param string|null $collectionName
     * @param string|null $diskName
     * @param bool $removeOldMedia
     * @return Media[]
     */
    public function addMultiMedia(JsonResource|string|int $id,array $files,?string $collectionName = 'default',?string $diskName = '',bool $removeOldMedia = false);
}

<?php

namespace Callmeaf\Base\App\Traits\Model;

use Spatie\Sluggable\SlugOptions;

trait HasSlug
{
    use \Spatie\Sluggable\HasSlug;
    public static function slugColumn(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->usingLanguage('')->generateSlugsFrom(self::sluggableColumn())->saveSlugsTo(self::slugColumn())->doNotGenerateSlugsOnUpdate();
    }

    abstract static function sluggableColumn(): string;
}

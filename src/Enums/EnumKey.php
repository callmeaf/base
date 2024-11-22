<?php

namespace Callmeaf\Base\Enums;

enum EnumKey: string
{
    case USER = 'user';
    case CART = 'cart';
    case CONTINENT = 'continent';
    case COUNTRY = 'country';
    case PROVINCE = 'province';
    case PRODUCT_CATEGORY = 'product_category';
    case PRODUCT = 'product';
    case MEDIA = 'media';
    case SLUG = 'slug';
    case VARIATION = 'variation';
    case VARIATION_TYPE = 'variation_type';
}

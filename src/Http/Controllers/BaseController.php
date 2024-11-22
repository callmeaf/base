<?php

namespace Callmeaf\Base\Http\Controllers;

use App\Http\Controllers\Controller;
use Callmeaf\Base\Enums\EnumKey;
use Callmeaf\Base\Http\Requests\V1\Api\EnumsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BaseController extends Controller
{
    public function http(Request $request)
    {
        return Http::withToken(token: $request->bearerToken());
    }

    public function enums(string $key): array
    {
        $enums = [];
        foreach (explode(',',$key) as $key) {
            $enums[] = $this->enumDataByKey($key);
        }
        return $enums;
    }

    private function enumDataByKey(string $key): array
    {
        $enum = EnumKey::tryFrom($key);

        $data = match ($enum) {
            EnumKey::USER => [
                'statuses' => enumAsOptions(cases: \Callmeaf\User\Enums\UserStatus::cases(),languages: config('callmeaf-user.model')),
                'types' => enumAsOptions(cases: \Callmeaf\User\Enums\UserType::cases(),languages: config('callmeaf-user.model')),
            ],
            EnumKey::PRODUCT_CATEGORY => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Product\Enums\ProductCategoryStatus::cases(),languages: config('callmeaf-product-category.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Product\Enums\ProductCategoryType::cases(),languages: config('callmeaf-product-category.model')),
            ],
            EnumKey::PRODUCT => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Product\Enums\ProductStatus::cases(),languages: config('callmeaf-product.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Product\Enums\ProductType::cases(),languages: config('callmeaf-product.model')),
            ],
            EnumKey::CART => [
                'types' => enumAsOptions(cases: \Callmeaf\Cart\Enums\CartType::cases(),languages: config('callmeaf-cart.model')),
            ],
            EnumKey::CONTINENT => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Geography\Enums\ContinentStatus::cases(),languages: config('callmeaf-continent.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Geography\Enums\ContinentType::cases(),languages: config('callmeaf-continent.model')),
            ],
            EnumKey::COUNTRY => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Geography\Enums\CountryStatus::cases(),languages: config('callmeaf-country.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Geography\Enums\CountryType::cases(),languages: config('callmeaf-country.model')),
            ],
            EnumKey::PROVINCE => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Geography\Enums\ProvinceStatus::cases(),languages: config('callmeaf-province.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Geography\Enums\ProvinceType::cases(),languages: config('callmeaf-province.model')),
            ],
            EnumKey::MEDIA => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Media\Enums\MediaStatus::cases(),languages: config('callmeaf-media.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Media\Enums\MediaType::cases(),languages: config('callmeaf-media.model')),
                'collections' => enumAsOptions(cases: \Callmeaf\Media\Enums\MediaCollection::cases(),languages: config('callmeaf-media.model')),
                'disks' => enumAsOptions(cases: \Callmeaf\Media\Enums\MediaDisk::cases(),languages: config('callmeaf-media.model')),
            ],
            EnumKey::SLUG => [
                'types' => enumAsOptions(cases: \Callmeaf\Slug\Enums\SlugType::cases(),languages: config('callmeaf-slug.model')),
            ],
            EnumKey::VARIATION => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Variation\Enums\VariationStatus::cases(),languages: config('callmeaf-variation.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Variation\Enums\VariationNature::cases(),languages: config('callmeaf-variation.model')),
            ],
            EnumKey::VARIATION_TYPE => [
                'statuses' => enumAsOptions(cases: \Callmeaf\Variation\Enums\VariationTypeStatus::cases(),languages: config('callmeaf-variation.model')),
                'types' => enumAsOptions(cases: \Callmeaf\Variation\Enums\VariationTypeCat::cases(),languages: config('callmeaf-variation.model')),
            ],
            default => [
                'message' => "Match case not found. key: {$key} undefined."
            ],
        };

        if($enum) {
            $data = [
                $enum->value => $data,
            ];
        }

        return $data;
    }
}

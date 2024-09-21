<?php

namespace Callmeaf\Base\Traits;

use Callmeaf\Cart\Enums\CartType;
use Callmeaf\Cart\Services\V1\CartService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCarts
{
    protected static function bootHasCarts(): void
    {
        static::created(function(Model $model) {
            /**
             * @var CartService $cartService
             */
            $cartService = app(config('callmeaf-cart.service'));
            $model->carts()->create($cartService->mergeData([
                // merge your default data
            ]));
        });
    }

    public function carts(): HasMany
    {
        return $this->hasMany(config('callmeaf-cart.model'));
    }

    public function currentCart(): Attribute
    {
        return Attribute::get(
            fn() => $this->carts()->firstWhere(column: 'type',operator: CartType::CURRENT->value),
        );
    }

    public function futureCart(): Attribute
    {
        return Attribute::get(
            fn() => $this->carts()->firstWhere(column: 'type',operator: CartType::FUTURE->value),
        );
    }
}

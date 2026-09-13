<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Applied to every tenant-owned model (Customer, Loan, JewelleryType, ...).
 * Automatically scopes all queries to the authenticated user's tenant, and
 * stamps tenant_id on create — so a controller can never accidentally leak
 * or write another tenant's data, and never needs to pass tenant_id itself.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}

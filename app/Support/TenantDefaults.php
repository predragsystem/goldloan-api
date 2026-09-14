<?php

namespace App\Support;

use App\Models\JewelleryQuality;
use App\Models\JewelleryType;
use App\Models\Tenant;

/**
 * Copies a starter jewellery type/quality list onto a newly signed-up
 * tenant, matching the old app's master data (JewelleryType.php) so
 * lenders migrating from it see familiar categories on day one. Each
 * tenant's copy is independent and editable afterward.
 */
class TenantDefaults
{
    private const TYPES = [
        'Earring', 'Ring', 'Pendant', 'Necklace', 'Mangalsutras',
        'Bracelet', 'Bangle', 'Anklet', 'Bellychain', 'Waistornament',
    ];

    // The old app's jewellery_quality table shipped empty — this starter
    // list is a reasonable default (common gold purities), not carried
    // over from existing data. Edit/replace per tenant as needed.
    private const QUALITIES = ['24 KT', '22 KT', '20 KT', '18 KT'];

    public static function seed(Tenant $tenant): void
    {
        foreach (self::TYPES as $name) {
            JewelleryType::create(['tenant_id' => $tenant->id, 'name' => $name]);
        }

        foreach (self::QUALITIES as $name) {
            JewelleryQuality::create(['tenant_id' => $tenant->id, 'name' => $name]);
        }
    }
}

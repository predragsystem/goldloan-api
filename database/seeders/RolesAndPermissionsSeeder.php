<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Global default roles + permissions, shared by every tenant
     * (tenant_id is null on these rows). A tenant-specific custom
     * role can be added later with tenant_id set.
     */
    public function run(): void
    {
        $permissions = [
            'staff.manage'      => 'Add/edit/disable staff logins',
            'billing.manage'    => 'View & manage subscription/billing',
            'customers.manage'  => 'Create/edit customers',
            'loans.manage'      => 'Create loans, take payments, top-up',
            'loans.close'       => 'Close/settle a loan',
            'masterdata.manage' => 'Manage jewellery type/quality lists',
            'reports.view'      => 'View reports & dashboard',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $owner = Role::firstOrCreate(
            ['tenant_id' => null, 'slug' => 'owner'],
            ['name' => 'Owner', 'is_default' => true]
        );
        $owner->permissions()->sync(Permission::pluck('id'));

        $cashier = Role::firstOrCreate(
            ['tenant_id' => null, 'slug' => 'cashier'],
            ['name' => 'Cashier', 'is_default' => true]
        );
        $cashier->permissions()->sync(
            Permission::whereIn('slug', [
                'customers.manage', 'loans.manage', 'loans.close',
                'masterdata.manage', 'reports.view',
            ])->pluck('id')
        );
    }
}

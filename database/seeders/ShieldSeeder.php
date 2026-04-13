<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["ViewAny:CategoryResource","View:CategoryResource","Create:CategoryResource","Update:CategoryResource","Delete:CategoryResource","Restore:CategoryResource","ForceDelete:CategoryResource","ForceDeleteAny:CategoryResource","RestoreAny:CategoryResource","Replicate:CategoryResource","Reorder:CategoryResource","ViewAny:CouponCodeResource","View:CouponCodeResource","Create:CouponCodeResource","Update:CouponCodeResource","Delete:CouponCodeResource","Restore:CouponCodeResource","ForceDelete:CouponCodeResource","ForceDeleteAny:CouponCodeResource","RestoreAny:CouponCodeResource","Replicate:CouponCodeResource","Reorder:CouponCodeResource","ViewAny:CrowdfundingProductsResource","View:CrowdfundingProductsResource","Create:CrowdfundingProductsResource","Update:CrowdfundingProductsResource","Delete:CrowdfundingProductsResource","Restore:CrowdfundingProductsResource","ForceDelete:CrowdfundingProductsResource","ForceDeleteAny:CrowdfundingProductsResource","RestoreAny:CrowdfundingProductsResource","Replicate:CrowdfundingProductsResource","Reorder:CrowdfundingProductsResource","ViewAny:OrderResource","View:OrderResource","Create:OrderResource","Update:OrderResource","Delete:OrderResource","Restore:OrderResource","ForceDelete:OrderResource","ForceDeleteAny:OrderResource","RestoreAny:OrderResource","Replicate:OrderResource","Reorder:OrderResource","ViewAny:ProductResource","View:ProductResource","Create:ProductResource","Update:ProductResource","Delete:ProductResource","Restore:ProductResource","ForceDelete:ProductResource","ForceDeleteAny:ProductResource","RestoreAny:ProductResource","Replicate:ProductResource","Reorder:ProductResource","ViewAny:UserResource","View:UserResource","Create:UserResource","Update:UserResource","Delete:UserResource","Restore:UserResource","ForceDelete:UserResource","ForceDeleteAny:UserResource","RestoreAny:UserResource","Replicate:UserResource","Reorder:UserResource","ViewAny:RoleResource","View:RoleResource","Create:RoleResource","Update:RoleResource","Delete:RoleResource","Restore:RoleResource","ForceDelete:RoleResource","ForceDeleteAny:RoleResource","RestoreAny:RoleResource","Replicate:RoleResource","Reorder:RoleResource"]},{"name":"\\u8fd0\\u8425","guard_name":"web","permissions":["ViewAny:CategoryResource","View:CategoryResource","Create:CategoryResource","Update:CategoryResource","Delete:CategoryResource","Restore:CategoryResource","ForceDelete:CategoryResource","ForceDeleteAny:CategoryResource","RestoreAny:CategoryResource","Replicate:CategoryResource","Reorder:CategoryResource","ViewAny:CouponCodeResource","View:CouponCodeResource","Create:CouponCodeResource","Update:CouponCodeResource","Delete:CouponCodeResource","Restore:CouponCodeResource","ForceDelete:CouponCodeResource","ForceDeleteAny:CouponCodeResource","RestoreAny:CouponCodeResource","Replicate:CouponCodeResource","Reorder:CouponCodeResource","ViewAny:CrowdfundingProductsResource","View:CrowdfundingProductsResource","Create:CrowdfundingProductsResource","Update:CrowdfundingProductsResource","Delete:CrowdfundingProductsResource","Restore:CrowdfundingProductsResource","ForceDelete:CrowdfundingProductsResource","ForceDeleteAny:CrowdfundingProductsResource","RestoreAny:CrowdfundingProductsResource","Replicate:CrowdfundingProductsResource","Reorder:CrowdfundingProductsResource","ViewAny:OrderResource","View:OrderResource","Create:OrderResource","Update:OrderResource","Delete:OrderResource","Restore:OrderResource","ForceDelete:OrderResource","ForceDeleteAny:OrderResource","RestoreAny:OrderResource","Replicate:OrderResource","Reorder:OrderResource","ViewAny:ProductResource","View:ProductResource","Create:ProductResource","Update:ProductResource","Delete:ProductResource","Restore:ProductResource","ForceDelete:ProductResource","ForceDeleteAny:ProductResource","RestoreAny:ProductResource","Replicate:ProductResource","Reorder:ProductResource"]}]';
        $directPermissions = '[{"name":"ViewAny:User","guard_name":"web"},{"name":"View:User","guard_name":"web"},{"name":"Create:User","guard_name":"web"},{"name":"Update:User","guard_name":"web"},{"name":"Delete:User","guard_name":"web"},{"name":"Restore:User","guard_name":"web"},{"name":"ForceDelete:User","guard_name":"web"},{"name":"ForceDeleteAny:User","guard_name":"web"},{"name":"RestoreAny:User","guard_name":"web"},{"name":"Replicate:User","guard_name":"web"},{"name":"Reorder:User","guard_name":"web"},{"name":"ViewAny:Role","guard_name":"web"},{"name":"View:Role","guard_name":"web"},{"name":"Create:Role","guard_name":"web"},{"name":"Update:Role","guard_name":"web"},{"name":"Delete:Role","guard_name":"web"},{"name":"Restore:Role","guard_name":"web"},{"name":"ForceDelete:Role","guard_name":"web"},{"name":"ForceDeleteAny:Role","guard_name":"web"},{"name":"RestoreAny:Role","guard_name":"web"},{"name":"Replicate:Role","guard_name":"web"},{"name":"Reorder:Role","guard_name":"web"},{"name":"ViewAny:Product","guard_name":"web"},{"name":"View:Product","guard_name":"web"},{"name":"Create:Product","guard_name":"web"},{"name":"Update:Product","guard_name":"web"},{"name":"Delete:Product","guard_name":"web"},{"name":"Restore:Product","guard_name":"web"},{"name":"ForceDelete:Product","guard_name":"web"},{"name":"ForceDeleteAny:Product","guard_name":"web"},{"name":"RestoreAny:Product","guard_name":"web"},{"name":"Replicate:Product","guard_name":"web"},{"name":"Reorder:Product","guard_name":"web"},{"name":"ViewAny:Category","guard_name":"web"},{"name":"View:Category","guard_name":"web"},{"name":"Create:Category","guard_name":"web"},{"name":"Update:Category","guard_name":"web"},{"name":"Delete:Category","guard_name":"web"},{"name":"Restore:Category","guard_name":"web"},{"name":"ForceDelete:Category","guard_name":"web"},{"name":"ForceDeleteAny:Category","guard_name":"web"},{"name":"RestoreAny:Category","guard_name":"web"},{"name":"Replicate:Category","guard_name":"web"},{"name":"Reorder:Category","guard_name":"web"},{"name":"ViewAny:Order","guard_name":"web"},{"name":"View:Order","guard_name":"web"},{"name":"Create:Order","guard_name":"web"},{"name":"Update:Order","guard_name":"web"},{"name":"Delete:Order","guard_name":"web"},{"name":"Restore:Order","guard_name":"web"},{"name":"ForceDelete:Order","guard_name":"web"},{"name":"ForceDeleteAny:Order","guard_name":"web"},{"name":"RestoreAny:Order","guard_name":"web"},{"name":"Replicate:Order","guard_name":"web"},{"name":"Reorder:Order","guard_name":"web"},{"name":"ViewAny:CouponCode","guard_name":"web"},{"name":"View:CouponCode","guard_name":"web"},{"name":"Create:CouponCode","guard_name":"web"},{"name":"Update:CouponCode","guard_name":"web"},{"name":"Delete:CouponCode","guard_name":"web"},{"name":"Restore:CouponCode","guard_name":"web"},{"name":"ForceDelete:CouponCode","guard_name":"web"},{"name":"ForceDeleteAny:CouponCode","guard_name":"web"},{"name":"RestoreAny:CouponCode","guard_name":"web"},{"name":"Replicate:CouponCode","guard_name":"web"},{"name":"Reorder:CouponCode","guard_name":"web"}]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_check::in","view_any_check::in","create_check::in","update_check::in","delete_check::in","delete_any_check::in","view_client","view_any_client","create_client","update_client","delete_client","delete_any_client","view_membership","view_any_membership","create_membership","update_membership","delete_membership","delete_any_membership","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_subscription","view_any_subscription","create_subscription","update_subscription","delete_subscription","delete_any_subscription","view_user","view_any_user","create_user","update_user","delete_user","delete_any_user","widget_StatsOverview"]},{"name":"owner","guard_name":"web","permissions":["view_check::in","view_any_check::in","create_check::in","update_check::in","delete_check::in","delete_any_check::in","view_client","view_any_client","create_client","update_client","delete_client","delete_any_client","view_membership","view_any_membership","create_membership","update_membership","delete_membership","delete_any_membership","view_subscription","view_any_subscription","create_subscription","update_subscription","delete_subscription","delete_any_subscription","view_user","view_any_user","create_user","update_user","delete_user","delete_any_user"]},{"name":"admin","guard_name":"web","permissions":["view_check::in","view_any_check::in","create_check::in","update_check::in","delete_check::in","delete_any_check::in","view_client","view_any_client","create_client","update_client","delete_client","delete_any_client","view_membership","view_any_membership","view_subscription","view_any_subscription","create_subscription","view_user","view_any_user"]},{"name":"trainer","guard_name":"web","permissions":["view_membership","view_any_membership","view_subscription","view_any_subscription"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

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
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}

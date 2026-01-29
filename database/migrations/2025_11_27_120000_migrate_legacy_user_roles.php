<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MigrateLegacyUserRoles extends Migration
{
    /**
     * Run the migrations.
     * This migration creates the canonical roles (`customer`, `staff`, `manager`) if missing
     * and assigns them to users based on the legacy `users.role` column values.
     * It does not drop the legacy column to keep the operation safe; that can be removed later.
     */
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'role')) {
            // Nothing to migrate
            return;
        }

        // Ensure roles exist
        $roles = ['customer', 'staff', 'manager'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Map legacy role values to spatie roles
        DB::table('users')->select('id', 'role')->whereNotNull('role')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $u) {
                $legacy = strtolower(trim($u->role));

                // Basic mapping — extend if you have other legacy role names
                switch ($legacy) {
                    case 'admin':
                    case 'manager':
                    case 'quản lý':
                        $roleName = 'manager';
                        break;
                    case 'staff':
                    case 'nhân viên':
                        $roleName = 'staff';
                        break;
                    default:
                        // default to customer for anything else
                        $roleName = 'customer';
                }

                try {
                    $user = User::find($u->id);
                    if ($user) {
                        // assignRole is idempotent and will not duplicate
                        $user->assignRole($roleName);
                    }
                } catch (\Exception $ex) {
                    // swallow exceptions to avoid blocking migrations; log for manual inspection
                    DB::table('migrations')->insert([
                        'migration' => 'migrate_legacy_user_roles_error_'.$u->id,
                        'batch' => 0,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * This will remove the three roles created by this migration (and any role assignments to users).
     */
    public function down()
    {
        $roles = ['customer', 'staff', 'manager'];
        foreach ($roles as $r) {
            if ($role = Role::where('name', $r)->first()) {
                // remove model_has_roles entries
                DB::table('model_has_roles')->where('role_id', $role->id)->delete();
                $role->delete();
            }
        }
    }
}

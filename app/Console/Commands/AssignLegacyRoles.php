<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignLegacyRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:legacy-roles {--dry-run : Do not persist changes, only show counts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Spatie roles to users based on legacy users.role values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! DB::table('users')->count()) {
            $this->info('No users found in database.');
            return 0;
        }

        $roles = ['customer', 'staff', 'manager'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $counts = ['customer' => 0, 'staff' => 0, 'manager' => 0, 'skipped' => 0];

        $bar = $this->output->createProgressBar(DB::table('users')->count());
        $bar->start();

        DB::table('users')->select('id', 'role')->orderBy('id')->chunk(200, function ($users) use (&$counts, $bar) {
            foreach ($users as $u) {
                $legacy = $u->role ? strtolower(trim($u->role)) : null;

                if (! $legacy) {
                    $counts['skipped']++;
                    $bar->advance();
                    continue;
                }

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
                        $roleName = 'customer';
                }

                try {
                    if (! $this->option('dry-run')) {
                        $user = User::find($u->id);
                        if ($user) {
                            $user->assignRole($roleName);
                            $counts[$roleName]++;
                        } else {
                            $counts['skipped']++;
                        }
                    } else {
                        $counts[$roleName]++;
                    }
                } catch (\Exception $ex) {
                    $this->error("Failed for user {$u->id}: " . $ex->getMessage());
                    $counts['skipped']++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->line('');

        $this->info('Mapping complete. Summary:');
        $this->table(['role', 'count'], [
            ['manager', $counts['manager']],
            ['staff', $counts['staff']],
            ['customer', $counts['customer']],
            ['skipped (no legacy role or error)', $counts['skipped']],
        ]);

        if ($this->option('dry-run')) {
            $this->info('Dry run - no database changes were made.');
        } else {
            $this->info('Roles assigned. Review results before dropping legacy column.');
        }

        return 0;
    }
}

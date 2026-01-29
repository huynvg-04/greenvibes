<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomerProfile;
use App\Models\MembershipTier;
use Carbon\Carbon;

class CheckMembershipExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra và tụt 1 hạng thành viên khi hết hạn';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();

        $expiredProfiles = CustomerProfile::whereNotNull('level_expires_at')
            ->where('level_expires_at', '<', $today)
            ->get();

        $count = 0;

        foreach ($expiredProfiles as $profile) {
            $currentTier = MembershipTier::where('name', $profile->level)->first();

            if ($currentTier) {

                $lowerTier = MembershipTier::where('priority', '<', $currentTier->priority)
                    ->orderBy('priority', 'desc')
                    ->first();

                if ($lowerTier) {

                    $profile->level = $lowerTier->name;

                    $profile->level_expires_at = Carbon::now()->addYear();

                    $this->info("User {$profile->user_id}: Tụt từ {$currentTier->name} xuống {$lowerTier->name}");
                } else {
                    $profile->level_expires_at = null;

                    $this->info("User {$profile->user_id}: Đã ở hạng thấp nhất. Xóa hạn sử dụng.");
                }

                $profile->save();
                $count++;
            }
        }

        $this->info("Hoàn tất quét. Đã xử lý {$count} thành viên.");
    }
}

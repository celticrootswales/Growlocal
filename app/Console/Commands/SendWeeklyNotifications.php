<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\WeeklyCropPlan;
use App\Notifications\GrowerEstimateReminder;
use App\Notifications\DistributorEstimateAlert;
use App\Notifications\AdminMissingEstimatesReport;

class SendWeeklyNotifications extends Command
{
    // These belong directly in the class, NOT inside any function
    protected $signature = 'notifications:weekly';
    protected $description = 'Send weekly notifications to growers, distributors, and admin';

    public function handle()
    {
        // Grower notifications
        $growers = User::role('grower')->get();
        foreach ($growers as $grower) {
            $grower->notify(new GrowerEstimateReminder());
        }

        // Distributor notifications
        $distributors = User::role('distributor')->get();
        foreach ($distributors as $distributor) {
            $distributor->notify(new DistributorEstimateAlert());
        }

        // Admin summary (could be compiled logic)
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminMissingEstimatesReport());
        }

        $this->info('✅ Weekly notifications sent.');
    }
}
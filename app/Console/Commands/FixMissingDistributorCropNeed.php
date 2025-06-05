<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GrowerCropCommitment;
use App\Models\DistributorCropNeed;

class FixMissingDistributorCropNeed extends Command
{
    protected $signature = 'fix:missing-commitment-links';
    protected $description = 'Fix missing distributor_crop_need_id in grower_crop_commitments';

    public function handle()
    {
        $broken = GrowerCropCommitment::whereNull('distributor_crop_need_id')->get();
        $fixed = 0;

        foreach ($broken as $commitment) {
            $grower = $commitment->grower;
            $offeringId = $commitment->crop_offering_id;

            $distributorIds = $grower->distributors->pluck('id');

            $match = DistributorCropNeed::where('crop_offering_id', $offeringId)
                ->whereIn('distributor_id', $distributorIds)
                ->first();

            if ($match) {
                $commitment->distributor_crop_need_id = $match->id;
                $commitment->save();
                $fixed++;
            }
        }

        $this->info("Fixed $fixed commitment(s).");
    }
}
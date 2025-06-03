<?php

namespace App\Http\Controllers;

use App\Models\WeeklyCropPlan;
use Illuminate\Http\Request;

class DistributorWeeklyOverviewController extends Controller
{
    public function index()
    {
        $weeklyPlans = WeeklyCropPlan::with([
            'commitment.grower',
            'commitment.distributorCropNeed.cropOffering',
            'estimate',
        ])
        ->whereHas('commitment', function ($q) {
            $q->whereHas('distributorCropNeed', function ($sub) {
                $sub->where('distributor_id', auth()->id());
            });
        })
        ->orderBy('week')
        ->get();

        return view('distributor.weekly_overview.index', compact('weeklyPlans'));
    }
}
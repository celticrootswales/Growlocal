<?php

namespace App\Http\Controllers;

use App\Models\WeeklyCropPlan;
use App\Models\WeeklyEstimate;
use Illuminate\Http\Request;

class GrowerWeeklyEstimateController extends Controller
{
    public function index()
    {
        $plans = WeeklyCropPlan::with(['commitment.distributorCropNeed.cropOffering'])
            ->whereHas('commitment', function ($q) {
                $q->where('grower_id', auth()->id());
            })
            ->orderBy('week')
            ->get();

        return view('grower.weekly_estimates.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weekly_crop_plan_id' => 'required|exists:weekly_crop_plans,id',
            'estimated_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        WeeklyEstimate::updateOrCreate(
            [
                'weekly_crop_plan_id' => $request->weekly_crop_plan_id,
                'grower_id' => auth()->id(),
            ],
            [
                'estimated_quantity' => $request->estimated_quantity,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Weekly estimate submitted.');
    }
}

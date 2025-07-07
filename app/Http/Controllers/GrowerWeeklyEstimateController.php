<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyAllocation;
use App\Models\WeeklyEstimate;

class GrowerWeeklyEstimateController extends Controller
{
    public function index(Request $request)
    {
        $growerId = auth()->id();

        $showAll = $request->boolean('all');

        $query = WeeklyAllocation::with([
            'commitment.distributorNeed.cropOffering',
            'commitment.distributorNeed.distributor',
            'estimate',
        ])->whereHas('commitment', function($q) use ($growerId) {
            $q->where('grower_id', $growerId);
        });

        if (!$showAll) {
            $query->whereDate('planned_date', '>=', now()->startOfWeek()->toDateString());
        }

        $allocations = $query->orderBy('planned_date')->get();

        // 🔧 Map weekly_crop_plan_id onto each allocation object (used in form)
        foreach ($allocations as $allocation) {
            $allocation->weekly_crop_plan_id = \App\Models\WeeklyCropPlan::where('grower_crop_commitment_id', $allocation->grower_crop_commitment_id)
                ->whereDate('week', \Carbon\Carbon::parse($allocation->planned_date)->startOfWeek())
                ->value('id');
        }

        return view('grower.weekly_estimates.index', compact('allocations', 'showAll'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'weekly_crop_plan_id' => 'required|exists:weekly_crop_plans,id',
            'estimated_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        \App\Models\WeeklyEstimate::updateOrCreate(
            [
                'weekly_crop_plan_id' => $request->weekly_crop_plan_id,
                'grower_id' => auth()->id(),
            ],
            [
                'estimated_quantity' => $request->estimated_quantity,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Weekly estimate saved.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeeklyAllocation;
use App\Models\WeeklyEstimate;

class GrowerWeeklyAllocationEstimateController extends Controller
{
    public function index()
    {
        $growerId = auth()->id();

        $allocations = WeeklyAllocation::select('*') // or explicitly list fields
        ->with([
            'commitment.distributorNeed.cropOffering',
            'estimate',
        ])
        ->whereHas('commitment', function ($q) use ($growerId) {
            $q->where('grower_id', $growerId);
        })
        ->orderBy('planned_date')
        ->get();

        return view('grower.weekly_estimates.index', [
            'allocations' => $allocations,
        ]);
    }

   public function store(Request $request)
    {
        $request->validate([
            'weekly_allocation_id' => 'required|exists:weekly_allocations,id',
            'estimated_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Get the WeeklyAllocation including the commitment
        $allocation = \App\Models\WeeklyAllocation::with('commitment')->findOrFail($request->weekly_allocation_id);

        // ✅ Get the weekly_crop_plan_id from the allocation itself
        $weeklyCropPlanId = $allocation->weekly_crop_plan_id;

        // If it's missing, return an error
        if (!$weeklyCropPlanId) {
            return back()->withErrors(['weekly_crop_plan_id' => 'Missing crop plan ID for this allocation.']);
        }

        // Save or update the estimate
        WeeklyEstimate::updateOrCreate(
            [
                'weekly_crop_plan_id' => $weeklyCropPlanId,
                'grower_id' => auth()->id(),
            ],
            [
                'estimated_quantity' => $request->estimated_quantity,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Weekly estimate submitted.');
    }

    public function destroy($id)
    {
        $estimate = \App\Models\WeeklyEstimate::where('id', $id)
            ->where('grower_id', auth()->id())
            ->firstOrFail();

        $estimate->delete();

        return redirect()->route('grower.weekly-estimates.index')->with('success', 'Estimate deleted successfully.');
    }
}
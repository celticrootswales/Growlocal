<?php

namespace App\Http\Controllers;

use App\Models\CropPlan;
use Illuminate\Http\Request;

class GrowerCropPlanController extends Controller
{
    // Show all crop plans assigned to the authenticated grower
    public function index()
    {
        $plans = CropPlan::where('grower_id', auth()->id())
            ->orderBy('week')
            ->get();

        return view('grower.crop_plan', compact('plans'));
    }

    // Update the grower's estimate for a specific crop plan entry
    public function update(Request $request, $id)
    {
        $plan = CropPlan::where('id', $id)
            ->where('grower_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'grower_estimate' => 'required|numeric|min:0',
        ]);

        $plan->grower_estimate = $request->grower_estimate;
        $plan->save();

        return back()->with('success', 'Your estimate has been saved.');
    }
}
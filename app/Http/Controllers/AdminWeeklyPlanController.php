<?php

namespace App\Http\Controllers;

use App\Models\GrowerCropCommitment;
use App\Models\WeeklyCropPlan;
use Illuminate\Http\Request;

class AdminWeeklyPlanController extends Controller
{
    public function index()
    {
        $commitments = GrowerCropCommitment::with(['grower', 'distributorCropNeed.cropOffering', 'weeklyPlans'])->get();

        return view('admin.weekly_plans.index', compact('commitments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grower_crop_commitment_id' => 'required|exists:grower_crop_commitments,id',
            'week' => 'required|date',
            'expected_quantity' => 'required|numeric|min:0',
        ]);

        WeeklyCropPlan::updateOrCreate(
            [
                'grower_crop_commitment_id' => $request->grower_crop_commitment_id,
                'week' => $request->week,
            ],
            [
                'expected_quantity' => $request->expected_quantity,
            ]
        );

        return back()->with('success', 'Weekly plan updated.');
    }
}
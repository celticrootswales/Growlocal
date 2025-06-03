<?php

namespace App\Http\Controllers;

use App\Models\DistributorCropNeed;
use App\Models\GrowerCropCommitment;
use Illuminate\Http\Request;

class GrowerCommitmentController extends Controller
{
    public function index()
    {
        $needs = DistributorCropNeed::with('cropOffering', 'distributor')->get();
        $commitments = GrowerCropCommitment::where('grower_id', auth()->id())->get()->keyBy('distributor_crop_need_id');

        return view('grower.commitments.index', compact('needs', 'commitments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_crop_need_id' => 'required|exists:distributor_crop_needs,id',
            'committed_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        GrowerCropCommitment::updateOrCreate(
            [
                'grower_id' => auth()->id(),
                'distributor_crop_need_id' => $request->distributor_crop_need_id,
            ],
            [
                'committed_quantity' => $request->committed_quantity,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Commitment saved.');
    }
}
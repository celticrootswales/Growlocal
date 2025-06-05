<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrowerCropCommitment;
use App\Models\DistributorCropNeed;

class GrowerCommitmentController extends Controller
{
    public function index()
    {
        $growerId = auth()->id();

        // Show needs assigned to grower's distributors
        $availableNeeds = DistributorCropNeed::with(['cropOffering', 'distributor'])
            ->whereDoesntHave('growerCommitments', function ($query) use ($growerId) {
                $query->where('grower_id', $growerId);
            })
            ->get();

        // Show grower's commitments
        $commitments = GrowerCropCommitment::with(['distributorNeed.distributor', 'cropOffering'])
            ->where('grower_id', $growerId)
            ->get();

        return view('grower.commitments.index', compact('availableNeeds', 'commitments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_crop_need_id' => 'required|exists:distributor_crop_needs,id',
            'quantity' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        GrowerCropCommitment::create([
            'grower_id' => auth()->id(),
            'distributor_crop_need_id' => $request->distributor_crop_need_id,
            'committed_quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Commitment submitted!');
    }

    public function edit($id)
    {
        $commitment = GrowerCropCommitment::findOrFail($id);

        if ($commitment->grower_id !== auth()->id()) {
            abort(403);
        }

        return view('grower.commitments.edit', compact('commitment'));
    }

    public function update(Request $request, $id)
    {
        $commitment = GrowerCropCommitment::findOrFail($id);

        if ($commitment->grower_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'committed_quantity' => 'required|numeric|min:0.1',
        ]);

        $commitment->update([
            'committed_quantity' => $request->committed_quantity,
        ]);

        return back()->with('success', 'Commitment updated.');
    }

    public function destroy($id)
    {
        $commitment = GrowerCropCommitment::findOrFail($id);

        if ($commitment->grower_id !== auth()->id()) {
            abort(403);
        }

        $commitment->delete();

        return back()->with('success', 'Commitment deleted.');
    }
}
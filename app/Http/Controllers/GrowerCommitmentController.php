<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrowerCropCommitment;
use App\Models\DistributorCropNeed;

class GrowerCommitmentController extends Controller
{
    public function index(Request $request)
    {
        $grower = auth()->user();
        $distributorIds = $grower->distributors->pluck('id');

        // Get crop need IDs the grower already committed to
        $alreadyCommittedNeedIds = GrowerCropCommitment::where('grower_id', $grower->id)
            ->pluck('distributor_crop_need_id');

        // Build base query and exclude committed needs
        $query = DistributorCropNeed::with(['cropOffering', 'distributor'])
            ->withSum('growerCommitments as committed_total', 'committed_quantity')
            ->whereIn('distributor_id', $distributorIds)
            ->whereNotIn('id', $alreadyCommittedNeedIds); // 👈 key fix

        // Filter by distributor if set
        if ($request->filled('distributor')) {
            $query->where('distributor_id', $request->distributor);
        }

        // Filter by crop name if set
        if ($request->filled('crop')) {
            $query->whereHas('cropOffering', function ($q) use ($request) {
                $q->where('crop_name', 'like', '%' . $request->crop . '%');
            });
        }

        // Finalise result: calculate remaining quantity
        $availableNeeds = $query->get()->map(function ($need) {
            $need->remaining_quantity = $need->desired_quantity - ($need->committed_total ?? 0);
            return $need;
        });

        // Commitments already made by this grower
        $commitments = GrowerCropCommitment::with([
            'distributorNeed.distributor',
            'cropOffering',
            'distributorNeed.growerCommitments'
        ])->where('grower_id', $grower->id)
            ->get()
            ->map(function ($commitment) {
                $totalCommitted = $commitment->distributorNeed->growerCommitments->sum('committed_quantity');
                $myCommitted = $commitment->committed_quantity;
                $desired = $commitment->distributorNeed->desired_quantity;

                $availableBeforeMe = $desired - ($totalCommitted - $myCommitted);

                $commitment->over_committed_by = $myCommitted > $availableBeforeMe
                    ? $myCommitted - $availableBeforeMe
                    : 0;

                return $commitment;
            });

        return view('grower.commitments.index', compact('availableNeeds', 'commitments', 'grower'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_crop_need_id' => 'required|exists:distributor_crop_needs,id',
            'quantity' => 'required|numeric|min:0.1',
            'notes' => 'nullable|string',
        ]);

        $need = DistributorCropNeed::with('cropOffering')->findOrFail($request->distributor_crop_need_id);

        if ($need->cropOffering->is_locked) {
            return back()->with('error', 'This crop offering is locked and cannot be committed to.');
        }

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
        $commitment = GrowerCropCommitment::with('cropOffering')->findOrFail($id);

        if ($commitment->grower_id !== auth()->id()) {
            abort(403);
        }

        if ($commitment->cropOffering->is_locked) {
            return back()->with('error', 'This crop offering is locked and cannot be updated.');
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
        $commitment = GrowerCropCommitment::with('cropOffering')->findOrFail($id);

        if ($commitment->grower_id !== auth()->id()) {
            abort(403);
        }

        if ($commitment->cropOffering->is_locked) {
            return back()->with('error', 'This crop offering is locked and cannot be deleted.');
        }

        $commitment->delete();

        return back()->with('success', 'Commitment deleted.');
    }
    
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropOffering;
use App\Models\DistributorCropNeed;
use Illuminate\Support\Facades\Auth;

class DistributorCropNeedController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $term = $request->get('term');

        $query = CropOffering::with([
            'distributors',
            'growerCommitments.grower',
        ])
        ->whereHas('distributors', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        });

        if ($term) {
            $query->where('term', $term);
        }

        $offerings = $query->get();

        return view('distributor.crop_needs.index', compact('offerings', 'term'));
    }
    
    public function store(Request $request)
    {
        // ✅ Restrict to Admin only
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only Admins can create crop needs.');
        }

        $distributorId = Auth::id();
        $data = $request->input('needs', []);

        foreach ($data as $offeringId => $amount) {
            DistributorCropNeed::updateOrCreate(
                [
                    'distributor_id' => $distributorId,
                    'crop_offering_id' => $offeringId,
                ],
                [
                    'amount_needed' => $amount,
                ]
            );
        }

        return redirect()->back()->with('success', 'Crop needs updated successfully.');
    }
}
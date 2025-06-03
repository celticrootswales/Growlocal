<?php

namespace App\Http\Controllers;

use App\Models\CropOffering;
use App\Models\DistributorCropNeed;
use Illuminate\Http\Request;

class DistributorCropNeedController extends Controller
{
    public function index()
    {
        $distributor = auth()->user();
        $needs = \App\Models\DistributorCropNeed::with('cropOffering')->where('distributor_id', $distributor->id)->get();

        return view('distributor.crop_needs.index', compact('needs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'crop_offering_id' => 'required|exists:crop_offerings,id',
            'desired_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        DistributorCropNeed::updateOrCreate(
            [
                'distributor_id' => auth()->id(),
                'crop_offering_id' => $request->crop_offering_id,
            ],
            [
                'desired_quantity' => $request->desired_quantity,
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Crop need saved.');
    }
}
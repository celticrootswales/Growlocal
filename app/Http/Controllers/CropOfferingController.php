<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropOffering;
use App\Models\DistributorCropNeed;
use App\Models\User;

class CropOfferingController extends Controller
{
    public function index(Request $request)
    {
        $distributorFilter = $request->input('distributor');
        $termFilter = $request->input('term');
        $yearFilter = $request->input('year');

        $query = CropOffering::with('distributors')->orderBy('crop_name');

        if ($distributorFilter) {
            $query->whereHas('distributors', fn($q) =>
                $q->where('users.id', $distributorFilter)
            );
        }

        if ($termFilter) {
            $query->where('term', $termFilter);
        }

        if ($yearFilter) {
            $query->where('year', $yearFilter);
        }

        $offerings = $query->get();
        $distributors = User::role('distributor')->get();

        return view('admin.crop_offerings.index', compact('offerings', 'distributors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:2',
            'unit' => 'required|in:kg,ea',
            'year' => 'required|digits:4|integer|min:2023',
            'default_price' => 'required|numeric|min:0',
            'amount_needed' => 'nullable|numeric|min:0',
            'term' => 'nullable|string|max:255',
            'distributors' => 'nullable|array',
            'distributors.*' => 'exists:users,id',
        ]);

        // Emoji fallback
        if (empty($validated['icon'])) {
            $emojiMap = [
                'carrot' => '🥕', 'potato' => '🥔', 'tomato' => '🍅',
                'cucumber' => '🥒', 'broccoli' => '🥦', 'lettuce' => '🥬',
                'onion' => '🧅', 'corn' => '🌽', 'pepper' => '🫑',
                'mushroom' => '🍄', 'apple' => '🍎', 'orange' => '🍊',
                'banana' => '🍌', 'grape' => '🍇', 'strawberry' => '🍓',
                'watermelon' => '🍉', 'lemon' => '🍋', 'garlic' => '🧄',
                'peas' => '🫛', 'beans' => '🫘', 'pumpkin' => '🎃',
                'radish' => '🌶️',
            ];

            foreach ($emojiMap as $name => $emoji) {
                if (str_contains(strtolower($validated['crop_name']), $name)) {
                    $validated['icon'] = $emoji;
                    break;
                }
            }
        }

        $offering = CropOffering::create($validated);
        if ($request->filled('distributors')) {
            $offering->distributors()->sync($request->distributors);
        }

        return redirect()->route('admin.crop-offerings.index')->with('success', 'Crop offering added.');
    }

    public function edit($id)
    {
        $offering = CropOffering::with('distributors')->findOrFail($id);
        $distributors = User::role('distributor')->get();

        return view('admin.crop_offerings.edit', compact('offering', 'distributors'));
    }

    public function update(Request $request, $id)
    {
        $offering = CropOffering::findOrFail($id);

        $validated = $request->validate([
            'crop_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:2',
            'unit' => 'required|in:kg,ea',
            'year' => 'required|digits:4|integer|min:2023',
            'default_price' => 'required|numeric|min:0',
            'amount_needed' => 'nullable|numeric|min:0',
            'term' => 'nullable|string|max:255',
            'distributors' => 'nullable|array',
            'distributors.*' => 'exists:users,id',
        ]);

        $offering->update($validated);
        $offering->distributors()->sync($request->distributors ?? []);

        // ✅ Reset submitted flag
        $offering->submitted_to_distributors = false;
        $offering->save();

        return redirect()->route('admin.crop-offerings.index')->with('success', 'Crop offering updated and marked as not submitted.');
    }

    public function destroy($id)
    {
        $offering = CropOffering::findOrFail($id);
        $offering->distributors()->detach();
        $offering->delete();

        return redirect()->route('admin.crop-offerings.index')->with('success', 'Offering deleted.');
    }

    public function pushToNeeds($id)
    {
        $offering = CropOffering::with('distributors')->findOrFail($id);

        foreach ($offering->distributors as $distributor) {
            DistributorCropNeed::updateOrCreate(
                [
                    'crop_offering_id' => $offering->id,
                    'distributor_id' => $distributor->id,
                ],
                [
                    'desired_quantity' => $offering->amount_needed ?? 0,
                    'amount_needed' => $offering->amount_needed ?? 0,
                    'unit' => $offering->unit,
                    'term' => $offering->term,
                    'year' => $offering->year,
                ]
            );
        }

        return redirect()->back()->with('success', 'Crop needs pushed to distributors successfully!');
    }

    public function submitToDistributors($id)
    {
        $offering = \App\Models\CropOffering::with('distributors')->findOrFail($id);

        foreach ($offering->distributors as $distributor) {
            \App\Models\DistributorCropNeed::updateOrCreate(
                [
                    'crop_offering_id' => $offering->id,
                    'distributor_id' => $distributor->id,
                ],
                [
                    'desired_quantity' => $offering->amount_needed ?? 0,
                ]
            );
        }

        // ✅ Set flag so we know it's submitted
        $offering->submitted_to_distributors = true;
        $offering->save();

        return redirect()->back()->with('success', 'Crop offering submitted to distributors!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\CropOffering;
use App\Models\User;
use Illuminate\Http\Request;

class CropOfferingController extends Controller
{
    public function index(Request $request)
    {
        $query = CropOffering::with('distributors');

        if ($request->filled('distributor')) {
            $query->whereHas('distributors', function ($q) use ($request) {
                $q->where('users.id', $request->input('distributor'));
            });
        }

        if ($request->filled('term')) {
            $query->where('term', $request->input('term'));
        }

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        $offerings = $query->orderBy('crop_name')->get();

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

        // Fallback: assign emoji based on crop name if not provided
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

        $offering->update($validated);
        $offering->distributors()->sync($request->distributors ?? []);

        return redirect()->route('admin.crop-offerings.index')->with('success', 'Crop offering updated.');
    }

    public function destroy($id)
    {
        $offering = CropOffering::findOrFail($id);
        $offering->distributors()->detach();
        $offering->delete();

        return redirect()->route('admin.crop-offerings.index')->with('success', 'Offering deleted.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DeliveryNote;
use App\Models\GrowerCropCommitment;

class HomeController extends Controller
{
    public function index()
    {
        // Number of Growers (Welsh, or all, adjust query as needed)
        $growersCount = User::role('grower')->count();

        // Veg Traced: Sum all boxes delivered (kg) for 2025
        $vegTraced = \App\Models\DeliveryBox::whereHas('note', function($q) {
            $q->whereYear('created_at', 2025);
        })->sum('quantity');

        // Meals delivered: Example, count of notes for schools (or total if you prefer)
        $meals = DeliveryNote::whereYear('created_at', 2025)
                    ->whereNotNull('school_id') // If you track schools
                    ->count();

        // Traceability: Percent of delivery notes with trace number (should be 100%)
        $totalNotes = DeliveryNote::whereYear('created_at', 2025)->count();
        $traceNotes = DeliveryNote::whereYear('created_at', 2025)->whereNotNull('traceability_number')->count();
        $tracePercent = $totalNotes > 0 ? round(($traceNotes / $totalNotes) * 100, 1) : 0;

        // Value to Growers: Sum up committed value for 2025
        $growerValue = GrowerCropCommitment::with('cropOffering')
            ->whereYear('created_at', 2025)
            ->get()
            ->sum(function ($c) {
                return ($c->committed_quantity ?? 0) * ($c->cropOffering->default_price ?? 0);
            });

        return view('welcome', compact('growersCount', 'vegTraced', 'meals', 'tracePercent', 'growerValue'));
    }
}
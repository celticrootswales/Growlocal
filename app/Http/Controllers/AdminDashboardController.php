<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropOffering;
use App\Models\DeliveryNote;

class AdminDashboardController extends Controller
{
public function index(Request $request)
{
    $year = $request->get('year', date('Y'));

    // Key stats
    $growersCount = \App\Models\User::role('grower')->count();
    $distributorsCount = \App\Models\User::role('distributor')->count();
    $offerings = \App\Models\CropOffering::with('distributors')
        ->where('year', $year)
        ->get();
    $offeringsCount = $offerings->count();
    $deliveriesCount = \App\Models\DeliveryNote::count();

    // Total potential income from all crop offerings
    $totalPotentialIncome = $offerings->sum(function ($offering) {
        return $offering->default_price * ($offering->amount_needed ?? 0);
    });

    // Income grouped by term
    $incomeByTerm = $offerings->isNotEmpty()
        ? $offerings->groupBy('term')->map(function ($group) {
            return $group->sum(function ($item) {
                return $item->default_price * ($item->amount_needed ?? 0);
            });
        })
        : collect();

    // Top distributors by how many offerings they're linked to
    $distributorStats = [];
    foreach ($offerings as $offering) {
        foreach ($offering->distributors as $dist) {
            if (!isset($distributorStats[$dist->id])) {
                $distributorStats[$dist->id] = (object)[
                    'name' => $dist->name,
                    'offerings_count' => 0,
                ];
            }
            $distributorStats[$dist->id]->offerings_count += 1;
        }
    }
    $topDistributors = collect($distributorStats)
        ->sortByDesc('offerings_count')
        ->values();

    // Offerings with no amount set
    $cropsWithoutAmount = $offerings->whereNull('amount_needed')->count();

    // Recent delivery notes
    $recentNotes = \App\Models\DeliveryNote::with(['user', 'boxes'])
        ->latest()->take(5)->get();

    // Active recalls
    $activeRecalls = \App\Models\DeliveryNote::with('user')
        ->where('recalled', true)
        ->latest()
        ->take(5)
        ->get();

    // Any other stats you want, add them here...

    return view('admin.dashboard', [
        'growersCount' => $growersCount,
        'distributorsCount' => $distributorsCount,
        'offeringsCount' => $offeringsCount,
        'deliveriesCount' => $deliveriesCount,
        'totalPotentialIncome' => $totalPotentialIncome,
        'incomeByTerm' => $incomeByTerm,
        'topDistributors' => $topDistributors,
        'cropsWithoutAmount' => $cropsWithoutAmount,
        'totalOfferings' => $offerings->count(),
        'selectedYear' => $year,
        'recentNotes' => $recentNotes,
        'activeRecalls' => $activeRecalls,
    ]);
}
}
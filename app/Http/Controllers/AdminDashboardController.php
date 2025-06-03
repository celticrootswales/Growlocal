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

        $offerings = CropOffering::with('distributors')
            ->where('year', $year)
            ->get();

        $totalPotentialIncome = $offerings->sum(function ($offering) {
            return $offering->default_price * ($offering->amount_needed ?? 0);
        });

        $incomeByTerm = $offerings->isNotEmpty()
            ? $offerings->groupBy('term')->map(function ($group) {
                return $group->sum(function ($item) {
                    return $item->default_price * ($item->amount_needed ?? 0);
                });
            })
            : collect();

        // Build stats with IDs as keys, then convert to objects
        $distributorStats = [];

        foreach ($offerings as $offering) {
            foreach ($offering->distributors as $dist) {
                if (!isset($distributorStats[$dist->id])) {
                    $distributorStats[$dist->id] = (object)[
                        'name' => $dist->name,
                        'offerings_count' => 0
                    ];
                }
                $distributorStats[$dist->id]->offerings_count += 1;
            }
        }

        $topDistributors = collect($distributorStats)->sortByDesc('offerings_count')->values();

        $cropsWithoutAmount = $offerings->whereNull('amount_needed')->count();

        $recentNotes = DeliveryNote::with(['user', 'boxes'])->latest()->take(5)->get();

        $activeRecalls = DeliveryNote::with('user')
            ->where('recalled', true)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
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
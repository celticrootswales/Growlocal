<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CropOffering;
use App\Models\DeliveryNote;
use App\Models\User;

class AdminController extends Controller
{
    // 🧮 Dashboard logic for Yearly Crop Offerings
    public function dashboard(Request $request)
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

        $distributorStats = collect();
        foreach ($offerings as $offering) {
            foreach ($offering->distributors as $dist) {
                $distributorStats[$dist->id] = [
                    'name' => $dist->name,
                    'offerings_count' => ($distributorStats[$dist->id]['offerings_count'] ?? 0) + 1
                ];
            }
        }

        $topDistributors = collect($distributorStats->values());

        $cropsWithoutAmount = $offerings->whereNull('amount_needed')->count();

        $recentNotes = DeliveryNote::with(['user', 'boxes'])
            ->latest()
            ->take(5)
            ->get();

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

    // ✅ Admin view for all delivery notes
    public function viewNotes(Request $request)
    {
        $notes = \App\Models\DeliveryNote::with('user')->latest()->take(15)->get(); // adjust as needed
        $growers = \App\Models\User::role('grower')->get();

        // If you want recall count
        $recallsCount = \App\Models\DeliveryNote::where('recalled', true)->count();

        return view('admin.notes', compact('notes', 'growers', 'recallsCount'));
    }

    // ✅ Admin view for active recalls
    public function manageRecalls()
    {
        $recalls = DeliveryNote::with('user')
            ->where('recalled', true)
            ->latest()
            ->paginate(15);

        return view('admin.recalls', compact('recalls'));
    }

    // ✅ Admin action to issue a recall
    public function issueRecall($noteId)
    {
        $note = DeliveryNote::findOrFail($noteId);
        $note->recalled = true;
        $note->save();

        return redirect()->back()->with('success', 'Recall issued successfully.');
    }

    // ✅ Admin action to remove a recall
    public function removeRecall($noteId)
    {
        $note = DeliveryNote::findOrFail($noteId);
        $note->recalled = false;
        $note->save();

        return redirect()->back()->with('success', 'Recall removed successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use App\Models\CropOffering;               // yearly offerings
use App\Models\GrowerCropCommitment;       // commitments
use App\Models\WeeklyAllocation;
use App\Models\ProgrammeWeek;
use Carbon\Carbon;


class AdminWeeklyPlanController extends Controller
{
    /**
     * Show all locked offerings that actually have commitments,
     * grouped by distributor → grower.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);

        // 👉 fetch distributors for filter dropdown
        $distributors = User::role('distributor')->orderBy('name')->get();

        // base query: locked offerings with commitments
        $offerings = CropOffering::with(['distributors', 'growerCommitments'])
            ->where('year', $year)
            ->where('is_locked', true)
            ->whereHas('growerCommitments');

        // ★ filter by distributor
        if ($request->filled('distributor')) {
            $offerings->whereHas('distributors', fn ($q) =>
                $q->where('users.id', $request->distributor));
        }

        // ★ filter by term
        if ($request->filled('term')) {
            $offerings->where('term', $request->term);
        }

        $offerings = $offerings->orderBy('crop_name')->get();

        // distinct list of terms (for dropdown)
        $terms = CropOffering::where('year', $year)
                    ->pluck('term')->unique()->values();

        return view('admin.weekly-plans.index', compact(
            'offerings', 'year', 'distributors', 'terms'
        ));
    }

    /**
     * Weekly planning screen for one locked offering.
     */
    public function plan(Request $request, CropOffering $offering)
    {
        abort_if(! $offering->is_locked, 403);

        $offering->load([
            'growerCommitments.grower',
            'growerCommitments.weeklyAllocations' // 👈 Load this
        ]);

        $programmeWeeks = \App\Models\ProgrammeWeek::orderBy('date')->get();

        return view('admin.weekly-plans.plan', [
            'offering' => $offering,
            'programmeWeeks' => $programmeWeeks,
        ]);
    }

    public function save(Request $request, $growerId)
    {
        $grower = User::findOrFail($growerId);

        // TODO: iterate $request->input('weekly') and persist allocations.
        // For now just dump the payload so you can see it:
        // dd($request->input('weekly'));

        return back()->with('success', "Weekly plan saved for {$grower->name}!");
    }

    public function saveBatch(Request $request)
    {
        foreach ($request->weekly as $commitmentId => $rows) {
            foreach ($rows as $row) {
                // Skip invalid rows
                if (empty($row['date']) || empty($row['quantity'])) {
                    continue;
                }

                if (!empty($row['allocation_id'])) {
                    // ✅ Update existing allocation
                    WeeklyAllocation::where('id', $row['allocation_id'])
                        ->update([
                            'planned_date' => $row['date'],
                            'quantity' => $row['quantity'],
                        ]);
                } else {
                    // ✅ Create new allocation
                    WeeklyAllocation::create([
                        'grower_crop_commitment_id' => $commitmentId,
                        'planned_date' => $row['date'],
                        'quantity' => $row['quantity'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Weekly plans updated successfully.');
    }

    public function updateAllocation(Request $request, WeeklyAllocation $allocation)
    {
        $request->validate([
            'planned_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
        ]);

        $allocation->update([
            'planned_date' => $request->planned_date,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Weekly allocation updated.');
    }

    public function deleteAllocation(WeeklyAllocation $allocation)
    {
        $allocation->delete();

        return back()->with('success', 'Weekly allocation deleted.');
    }
}
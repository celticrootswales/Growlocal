<?php

namespace App\Http\Controllers;

use App\Models\CropPlan;
use App\Models\DeliveryNote;
use App\Models\Recall;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DistributorController extends Controller
{
    // Show delivery dashboard
    public function dashboard(Request $request)
    {
        $query = DeliveryNote::with(['user', 'boxes'])
            ->where('distributor_id', auth()->id());

        if ($request->filled('search')) {
            $query->where('traceability_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $notes = $query->latest()->get();

        if ($request->has('export') && $request->export === 'csv') {
            $csv = "Trace #,Grower,Status,Date\n";
            foreach ($notes as $note) {
                $csv .= "{$note->traceability_number},{$note->user->name},{$note->status},{$note->created_at->format('Y-m-d')}\n";
            }

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=delivery-notes.csv',
            ]);
        }

        return view('distributor.dashboard', compact('notes'));
    }

    // Issue a recall
    public function issueRecall(Request $request, $noteId)
    {
        $note = DeliveryNote::where('id', $noteId)
            ->where('distributor_id', auth()->id())
            ->firstOrFail();

        $note->recalled = true;
        $note->save();

        Recall::create([
            'delivery_note_id' => $noteId,
            'reason' => $request->input('reason', 'No reason provided'),
        ]);

        return back()->with('success', 'Batch recalled successfully.');
    }

    // Show list of recalls
    public function recallList(Request $request)
    {
        $query = DeliveryNote::with(['recall', 'user'])
            ->where('distributor_id', auth()->id())
            ->where('recalled', true);

        if ($request->has('search') && $request->search) {
            $query->where('traceability_number', 'like', '%' . $request->search . '%');
        }

        $notes = $query->latest()->get();

        return view('distributor.recalls', compact('notes'));
    }

    // Show crop plan view
    public function cropPlan()
    {
        $plans = CropPlan::with(['grower', 'distributor', 'commitments.grower'])
            ->where('distributor_id', auth()->id())
            ->latest()
            ->get();

        $growers = User::role('grower')->get();

        return view('distributor.crop_plan', compact('plans', 'growers'));
    }

    // Store new crop plan
    public function storeCropPlan(Request $request)
    {
        $request->validate([
            'week' => 'required|date',
            'crop_name' => 'required|string',
            'unit' => 'required|in:kg,ea',
            'expected_quantity' => 'required|numeric|min:1',
            'price_per_unit' => 'required|numeric|min:0',
            'grower_id' => 'required|exists:users,id',
        ]);

        CropPlan::create([
            'week' => $request->week,
            'crop_name' => $request->crop_name,
            'unit' => $request->unit,
            'expected_quantity' => $request->expected_quantity,
            'price_per_unit' => $request->price_per_unit,
            'grower_id' => $request->grower_id,
            'distributor_id' => auth()->id(),
        ]);

        return back()->with('success', 'Crop plan added.');
    }

    // Update existing crop plan (e.g. from modal)
    public function updateCropPlan(Request $request, $id)
    {
        $request->validate([
            'expected_quantity' => 'required|numeric|min:1',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        $plan = CropPlan::where('id', $id)
            ->where('distributor_id', auth()->id())
            ->firstOrFail();

        $plan->update([
            'expected_quantity' => $request->expected_quantity,
            'price_per_unit' => $request->price_per_unit,
        ]);

        return back()->with('success', 'Crop plan updated.');
    }

    // Delete crop plan
    public function deleteCropPlan($id)
    {
        $plan = CropPlan::where('id', $id)
            ->where('distributor_id', auth()->id())
            ->firstOrFail();

        $plan->delete();

        return back()->with('success', 'Crop plan deleted.');
    }
}
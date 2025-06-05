<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryBox;
use App\Models\Recall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\CropPlan;

class DeliveryNoteController extends Controller
{
    
    public function dashboard()
    {
        $user = auth()->user();

        $notes = DeliveryNote::with('boxes')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $recalled = DeliveryNote::with('recall')
            ->where('user_id', $user->id)
            ->where('recalled', true)
            ->get();

        // Load crop plans for this grower
        $plans = CropPlan::where('user_id', $user->id)->orderBy('week_start')->get();

        // Calculate supplied quantities from delivery notes
        $supplied = [];
        foreach ($plans as $plan) {
            $total = 0;
            foreach ($notes as $note) {
                foreach ($note->boxes as $box) {
                    if (
                        strtolower($box->crop) === strtolower($plan->crop) &&
                        $note->created_at->startOfWeek()->eq(\Carbon\Carbon::parse($plan->week_start)->startOfWeek())
                    ) {
                        $total += $box->quantity;
                    }
                }
            }
            $supplied[$plan->id] = $total;
        }

        return view('grower.dashboard', compact('notes', 'recalled', 'plans', 'supplied'));
    }

    public function index()
    {
        $user = auth()->user();

        $notes = DeliveryNote::with(['boxes', 'recall'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('grower.notes', compact('notes'));
    }

    
   
    public function create()
    {
        $user = auth()->user();
        $currentWeek = now()->startOfWeek()->toDateString();

        $weeklyPlans = \App\Models\WeeklyCropPlan::with(['commitment.cropOffering'])
            ->whereHas('commitment', fn($q) => $q->where('grower_id', $user->id))
            ->where('week', $currentWeek)
            ->get();

        $distributors = User::role('distributor')->get(); // ✅ Add this line

        return view('grower.delivery_notes.create', [
            'weeklyPlans' => $weeklyPlans,
            'distributors' => $distributors // ✅ And pass it to the view
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:users,id',
            'crops.*.name' => 'required|string',
            'crops.*.quantity' => 'required|numeric|min:0.01',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $note = new DeliveryNote();
        $note->user_id = auth()->id();
        $note->distributor_id = $request->distributor_id;
        $note->status = 'pending';
        $note->traceability_number = strtoupper(Str::random(8));

        if ($request->hasFile('invoice')) {
            $note->invoice_path = $request->file('invoice')->store('invoices', 'public');
        }

        $note->save();

        foreach ($request->crops as $crop) {
            $note->boxes()->create([
                'crop' => $crop['name'],
                'quantity' => $crop['quantity'],
                'label_code' => strtoupper(Str::random(8)), // Example label code
            ]);
        }

        return redirect()->route('grower.notes.index')->with('success', 'Delivery note created.');
    }

    public function markDelivered($id)
    {
        $note = DeliveryNote::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $note->update(['status' => 'Delivered']);

        return redirect()->back()->with('success', 'Marked as delivered.');
    }

    public function destroy($id)
    {
        $note = DeliveryNote::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Delete invoice if it exists
        if ($note->invoice_path && Storage::disk('public')->exists($note->invoice_path)) {
            Storage::disk('public')->delete($note->invoice_path);
        }

        // Delete associated boxes
        $note->boxes()->delete();

        $note->delete();

        return redirect()->back()->with('success', 'Delivery note deleted.');
    }

    public function generatePdf($id)
    {
        $note = DeliveryNote::with(['boxes', 'user'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.delivery-note', compact('note'));
        return $pdf->download("delivery-note-{$note->id}.pdf");
    }

    public function generateLabel($id)
    {
        $note = DeliveryNote::with('boxes')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.label', compact('note'));
        return $pdf->download("label-note-{$note->id}.pdf");
    }

    public function toggleRecall($id)
    {
        $note = DeliveryNote::findOrFail($id);
        $note->recalled = !$note->recalled;
        $note->save();

        return redirect()->back()->with('success', 'Recall status updated.');
    }

    public function recallList()
    {
        $notes = DeliveryNote::with(['recall', 'user'])
            ->where('recalled', true)
            ->latest()
            ->get();

        return view('admin.recalls', compact('notes'));
    }

    public function acknowledgeRecall($id)
    {
        $note = DeliveryNote::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('recalled', true)
            ->firstOrFail();

        $note->recall_acknowledged = true;
        $note->save();

        return back()->with('success', 'Recall acknowledged.');
    }
}


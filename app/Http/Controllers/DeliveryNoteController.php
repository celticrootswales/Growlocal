<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryBox;
use App\Models\Recall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

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

        return view('grower.dashboard', compact('notes', 'recalled'));
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

        $latest = DeliveryNote::with('boxes')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('grower.delivery_notes.create', compact('latest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'crops' => 'required|array',
            'quantities' => 'required|array',
            'destination' => 'required|string',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);

        $path = null;
        if ($request->hasFile('invoice')) {
            $path = $request->file('invoice')->store('invoices', 'public');
        }

        $note = new DeliveryNote();
        $note->user_id = auth()->id();
        $note->destination = $request->destination;
        $note->invoice_path = $path;
        $note->traceability_number = strtoupper(Str::random(10));
        $note->status = 'Pending';
        $note->recalled = false;
        $note->save();

        foreach ($request->crops as $i => $crop) {
            $note->boxes()->create([
                'crop' => $crop,
                'quantity' => $request->quantities[$i],
                'label_code' => strtoupper(Str::random(8)),
            ]);
        }

        return redirect()->route('grower.dashboard')->with('success', 'Delivery note and boxes submitted.');
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
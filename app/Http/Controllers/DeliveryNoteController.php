<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DeliveryBox;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNoteController extends Controller
{
   public function dashboard()
    {
        if (!auth()->user()?->hasRole('grower')) {
            abort(403, 'Unauthorized'); // or redirect somewhere
        }

        $notes = auth()->user()->deliveryNotes()->latest()->get();
        return view('grower.dashboard', compact('notes'));
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

    // 🛠️ Set values explicitly, avoid mass-assignment
    $note = new DeliveryNote();
    $note->user_id = auth()->id();
    $note->destination = $request->destination;
    $note->invoice_path = $path;
    $note->traceability_number = strtoupper(Str::random(10));
    $note->status = 'Pending';
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
}
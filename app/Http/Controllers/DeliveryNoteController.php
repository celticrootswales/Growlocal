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
use App\Models\CropOffering;
use App\Models\GrowerCropCommitment;

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

        $plans = CropPlan::where('user_id', $user->id)->orderBy('week_start')->get();

        $commitments = GrowerCropCommitment::with('cropOffering')
            ->where('grower_id', $user->id)
            ->whereYear('created_at', date('Y'))
            ->get();

        $estimatedValue = $commitments->sum(function ($commitment) {
            return ($commitment->committed_quantity ?? 0) * ($commitment->cropOffering->default_price ?? 0);
        });

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

        return view('grower.dashboard', compact('notes', 'recalled', 'plans', 'supplied', 'estimatedValue'));
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

        $commitments = GrowerCropCommitment::with('cropOffering')
            ->where('grower_id', $user->id)
            ->get();

        $cropOfferings = $commitments
            ->pluck('cropOffering')
            ->filter()
            ->unique('id')
            ->values();

        $distributors = User::role('distributor')->get();

        return view('grower.delivery_notes.create', [
            'cropOfferings' => $cropOfferings,
            'distributors' => $distributors,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:users,id',
            'crops.*.crop_offering_id' => 'required|exists:crop_offerings,id',
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
            $offering = CropOffering::find($crop['crop_offering_id']);

            $note->boxes()->create([
                'crop' => $offering->crop_name ?? 'Unknown',
                'quantity' => $crop['quantity'],
                'crop_offering_id' => $offering->id,
                'unit_type' => $offering->unit ?? 'unit', // <-- Add this line
                'label_code' => strtoupper(Str::random(8)),
            ]);
        }

        return redirect()->route('grower.notes.index')->with('success', 'Delivery note created.');
    }

    public function generatePdf($id)
    {
        $note = DeliveryNote::with([
            'boxes.cropOffering',
            'user',
            'distributor'
        ])
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        $totalValue = 0;

        foreach ($note->boxes as $box) {
            $offering = $box->cropOffering;

            // Fallback if relationship not loaded
            if (!$offering) {
                $offering = CropOffering::where('id', $box->crop_offering_id)->first();
            }

            $commitment = $offering
                ? GrowerCropCommitment::where('grower_id', $note->user_id)
                    ->where('crop_offering_id', $offering->id)
                    ->first()
                : null;

            $pricePerUnit = $commitment->price ?? $offering->default_price ?? 0;
            $unitType = $offering->unit ?? 'unit';

            $box->unit_type = $unitType;
            $box->price_per_unit = $pricePerUnit;
            $box->total_price = $box->quantity * $pricePerUnit;

            $totalValue += $box->total_price;
        }

        return Pdf::loadView('pdf.delivery-note', [
            'note' => $note,
            'totalValue' => $totalValue
        ])->download("delivery-note-{$note->id}.pdf");
    }

    public function generateLabel($id)
    {
        $note = DeliveryNote::with(['boxes.cropOffering', 'user', 'distributor'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        foreach ($note->boxes as $box) {
            $offering = $box->cropOffering;

            $box->unit_type = $offering->unit ?? 'unit';
            $box->term = $offering->term ?? 'N/A';
        }

        $pdf = Pdf::loadView('pdf.label', compact('note'));
        return $pdf->download("label-note-{$note->id}.pdf");
    }

    public function destroy($id)
    {
        $note = DeliveryNote::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($note->invoice_path && Storage::disk('public')->exists($note->invoice_path)) {
            Storage::disk('public')->delete($note->invoice_path);
        }

        $note->boxes()->delete();
        $note->delete();

        return redirect()->back()->with('success', 'Delivery note deleted.');
    }

    public function markDelivered($id)
    {
        $note = DeliveryNote::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $note->update(['status' => 'Delivered']);

        return redirect()->back()->with('success', 'Marked as delivered.');
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

    public function getOfferingsByDistributor($distributorId)
    {
        $user = auth()->user();

        $commitments = GrowerCropCommitment::with('cropOffering')
            ->where('grower_id', $user->id)
            ->get();

        $offeringIds = \DB::table('crop_offering_distributor')
            ->where('distributor_id', $distributorId)
            ->pluck('crop_offering_id');

        $offerings = $commitments
            ->pluck('cropOffering')
            ->filter(fn($o) => $offeringIds->contains($o->id))
            ->unique('id')
            ->values()
            ->map(function ($offering) {
                return [
                    'id' => $offering->id,
                    'label' => sprintf(
                        '%s (%s, %s) @ £%.2f',
                        $offering->crop_name,
                        $offering->unit ?? 'unit',
                        $offering->term ?? 'N/A',
                        $offering->default_price ?? 0
                    ),
                ];
            });

        return response()->json($offerings);
    }
}
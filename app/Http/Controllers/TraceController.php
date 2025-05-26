<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryBox;

class TraceController extends Controller
{
    public function show($code)
    {
        $box = DeliveryBox::where('label_code', $code)
            ->with(['deliveryNote', 'deliveryNote.grower'])
            ->first();

        if (!$box) {
            abort(404, 'Label not found.');
        }

        return view('trace.show', [
            'box' => $box,
            'note' => $box->deliveryNote,
            'grower' => $box->deliveryNote->grower ?? null,
        ]);
    }
}
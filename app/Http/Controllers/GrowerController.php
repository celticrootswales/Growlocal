<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;

class GrowerController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->hasRole('grower')) {
            $deliveryNotes = DeliveryNote::where('user_id', auth()->id())->latest()->get();
            return view('grower.dashboard', compact('deliveryNotes'));
        }

        abort(403, 'Unauthorized');
    }
}
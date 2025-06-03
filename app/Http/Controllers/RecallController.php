<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;

class RecallController extends Controller
{
    // Show all recalled delivery notes
    public function index()
    {
        $recalls = DeliveryNote::with('user')
            ->where('recalled', true)
            ->latest()
            ->paginate(10);

        return view('admin.recalls.index', compact('recalls'));
    }

    // Issue a recall for a delivery note
    public function issue(Request $request, $noteId)
    {
        $note = DeliveryNote::findOrFail($noteId);
        $note->recalled = true;
        $note->save();

        return redirect()->back()->with('success', 'Recall issued successfully.');
    }

    // Remove a recall from a delivery note
    public function remove(Request $request, $noteId)
    {
        $note = DeliveryNote::findOrFail($noteId);
        $note->recalled = false;
        $note->save();

        return redirect()->back()->with('success', 'Recall removed successfully.');
    }
}
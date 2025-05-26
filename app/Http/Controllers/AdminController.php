<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Models\Recall;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function viewNotes(Request $request)
    {
        $query = DeliveryNote::with(['boxes', 'user'])->latest();

        if ($request->filled('search')) {
            $query->where('traceability_number', 'like', '%' . $request->search . '%');
        }

        $notes = $query->get();

        return view('admin.notes', compact('notes'));
    }

    public function manageRecalls(Request $request)
    {
        $query = DeliveryNote::with('user')->where('recalled', true);

        // Apply traceability number search
        if ($request->filled('search')) {
            $query->where('traceability_number', 'like', '%' . $request->search . '%');
        }

        $notes = $query->orderBy('created_at', 'desc')->get();

        return view('admin.recalls', compact('notes'));
    }

    public function issueRecall(Request $request, $noteId)
    {
        $note = DeliveryNote::findOrFail($noteId);

        // Ensure recall reason is stored
        Recall::updateOrCreate(
            ['delivery_note_id' => $noteId],
            ['reason' => $request->input('reason', 'No reason provided')]
        );

        // Set recalled flag
        $note->recalled = true;
        $note->save();

        return back()->with('success', 'Recall issued for this batch.');
    }

    public function removeRecall($noteId)
    {
        $note = \App\Models\DeliveryNote::findOrFail($noteId);
        $note->recalled = false; // ✅ unset the recall flag
        $note->save();

        // Optional: also delete from the recalls table if you're tracking reasons separately
        \App\Models\Recall::where('delivery_note_id', $noteId)->delete();

        return redirect()->back()->with('success', 'Recall removed successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\GrowerCropCommitment;

class AdminGrowerController extends Controller
{
    public function index()
    {
        $growers = User::role('grower')->with('distributors')->get();
        return view('admin.growers.index', compact('growers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'business_name' => 'required',
            'distributors' => 'nullable|array'
        ]);

        $password = Str::random(10);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->business_name,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('grower');

        if ($request->filled('distributors')) {
            $user->distributors()->sync($request->distributors);
        }

        // Email can be queued here to notify user with credentials

        return redirect()->route('admin.growers.index')->with('success', 'Grower created!');
    }

    public function edit($id)
    {
        $grower = User::findOrFail($id);
        $distributors = User::role('distributor')->get();
        return view('admin.growers.edit', compact('grower', 'distributors'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->business_name;
        $user->email = $request->email;
        $user->save();

        $user->distributors()->sync($request->distributors ?? []);

        return redirect()->route('admin.growers.index')->with('success', 'Grower updated.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Grower deleted.');
    }

    public function showCommitments($growerId)
    {
        $grower = User::with('growerCropCommitments.cropOffering')->findOrFail($growerId);

        return view('admin.growers.commitments', compact('grower'));
    }

    public function toggleCommitmentLock($id)
    {
        $commitment = GrowerCropCommitment::findOrFail($id);
        $commitment->is_locked = ! $commitment->is_locked;
        $commitment->save();

        return back()->with('success', 'Commitment lock status updated.');
    }
}
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
    public function index(Request $request)
    {
        $growersQuery = User::role('grower')->with('distributors');
        $distributors = User::role('distributor')->get();

        if ($request->filled('distributor')) {
            $distributorId = $request->input('distributor');
            $growersQuery->whereHas('distributors', function ($q) use ($distributorId) {
                $q->where('users.id', $distributorId);
            });
        }

        $growers = $growersQuery->get();
        return view('admin.growers.index', compact('growers', 'distributors'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'business_name' => 'required',
            'distributors' => 'nullable|array',
            'password' => 'nullable|string|min:6'
        ]);

        $password = $request->filled('password') ? $request->password : Str::random(10);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'business_name' => $request->business_name,
            'password' => Hash::make($password),
        ]);

        $user->assignRole('grower');

        if ($request->filled('distributors')) {
            $user->distributors()->sync($request->distributors);
        }

        // 🔹 Email credentials (optional)
        Mail::to($user->email)->send(new GrowerCreated($user, $password));

        // 🔹 Show password back to admin
        return redirect()->route('admin.growers.index')
            ->with('success', 'Grower created! Temporary password: ' . $password);
    }

    public function edit($id)
    {
        $grower = User::findOrFail($id);
        $distributors = User::role('distributor')->get();
        return view('admin.growers.edit', compact('grower', 'distributors'));
    }

   public function update(Request $request, $id)
    {
        $grower = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $grower->id,
            'business_name' => 'required',
            'distributors' => 'nullable|array',
            'password' => 'nullable|string|min:6',
        ]);

        $grower->update([
            'email' => $request->email,
            'name' => $request->name,
            'business_name' => $request->business_name,
            'password' => $request->filled('password') ? Hash::make($request->password) : $grower->password,
        ]);

        $grower->distributors()->sync($request->distributors ?? []);

        return redirect()->route('admin.growers.edit', $grower->id)
            ->with('success', 'Grower updated' . ($request->filled('password') ? ' with new password.' : '.'));
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
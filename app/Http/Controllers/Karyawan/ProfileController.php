<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        return view('karyawan.profile', compact('employee'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:8|confirmed',
        ]);

        // Update data employee
        if ($user->employee) {
            $user->employee->update([
                'phone'   => $request->phone,
                'address' => $request->address,
            ]);
        }

        // Ganti password kalau diisi
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
            }
            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}

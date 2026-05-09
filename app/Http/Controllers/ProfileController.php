<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik user
        $totalTransactions = $user->transactions()->count();
        $totalSpent        = $user->transactions()->sum('total_amount');
        $totalReceipts     = $user->receipts()->count();
        $memberSince       = $user->created_at->translatedFormat('F Y');

        return view('profile.index', compact(
            'user',
            'totalTransactions',
            'totalSpent',
            'totalReceipts',
            'memberSince'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'email.unique' => 'Email sudah digunakan akun lain.',
            'avatar.max'   => 'Foto profil maksimal 2MB.',
        ]);

        // Upload avatar baru
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')
                ->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui! ✅');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])
                         ->with('tab', 'password');
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah! 🔐')
                     ->with('tab', 'password');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'confirm_delete' => 'required|in:HAPUS',
        ], [
            'confirm_delete.in' => 'Ketik HAPUS untuk konfirmasi.',
        ]);

        $user = Auth::user();
        Auth::logout();

        // Hapus semua data & file
        foreach ($user->receipts as $receipt) {
            Storage::disk('public')->delete($receipt->image_path);
        }
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
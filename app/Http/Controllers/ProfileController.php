<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $view = match ($user->role) {
            'manager' => 'manager.profil',
            'teknisi' => 'teknisi.profil',
            default => 'operator.profil',
        };

        return view($view, [
            'user' => $user,
            'bagianOptions' => config('sippm.acc_bagian_options.teknisi', []),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bagian' => ['nullable', 'string', 'max:100'],
            'kata_sandi_baru' => ['nullable', 'string'],
            'konfirmasi_kata_sandi_baru' => ['nullable', 'string'],
        ]);

        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;

        // Hanya Teknisi yang boleh mengganti Bagian/Keahlian sendiri; untuk
        // Operator & Manager, bagian diatur oleh Manager (readonly di form).
        if ($user->isTeknisi() && $request->filled('bagian')) {
            $user->bagian = $data['bagian'];
        }

        $newPass = $data['kata_sandi_baru'] ?? '';
        $confirmPass = $data['konfirmasi_kata_sandi_baru'] ?? '';

        // Field kosong dianggap "tidak ada perubahan kata sandi" -> hanya
        // simpan data lain, persis logika savePasswordChange() pada mockup.
        if ($newPass === '' && $confirmPass === '') {
            $user->save();

            return back()->with('success', 'Perubahan profil disimpan.');
        }

        if (strlen($newPass) < 8) {
            return back()
                ->withErrors(['kata_sandi_baru' => 'Kata sandi baru minimal 8 karakter.'])
                ->withInput();
        }

        if ($newPass !== $confirmPass) {
            return back()
                ->withErrors(['konfirmasi_kata_sandi_baru' => 'Konfirmasi kata sandi tidak cocok.'])
                ->withInput();
        }

        $user->password = Hash::make($newPass);
        $user->must_change_password = false;
        $user->save();

        return back()->with('success', 'Perubahan profil & kata sandi disimpan.');
    }
}

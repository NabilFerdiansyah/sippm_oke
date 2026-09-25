<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'operator');
        $tab = in_array($tab, ['operator', 'teknisi'], true) ? $tab : 'operator';

        $akun = User::where('role', $tab)
            ->orderBy('name')
            ->get()
            ->map(function (User $u) use ($tab) {
                $u->jumlah_laporan = $tab === 'operator'
                    ? $u->laporanDibuat()->whereNotIn('status', ['selesai', 'ditolak'])->count()
                    : $u->laporanDitugaskan()->whereIn('status', ['ditugaskan', 'dikerjakan', 'menunggu_validasi_akhir'])->count();

                return $u;
            });

        return view('manager.akun.index', [
            'tab' => $tab,
            'akun' => $akun,
        ]);
    }

    public function create(Request $request): View
    {
        $role = $request->query('role', 'operator');
        $role = in_array($role, ['operator', 'teknisi'], true) ? $role : 'operator';

        return view('manager.akun.create', [
            'role' => $role,
            'bagianOptions' => config("sippm.acc_bagian_options.$role", []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:operator,teknisi'],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bagian' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'bagian.required' => 'Area/Bagian wajib dipilih.',
        ]);

        $username = $this->generateUsername($data['name'], $data['role']);
        $tempPassword = $this->generateTempPassword();

        $user = User::create([
            'name' => $data['name'],
            'username' => $username,
            'password' => Hash::make($tempPassword),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'bagian' => $data['bagian'],
            'is_active' => true,
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('manager.akun.berhasil', $user)
            ->with('temp_password', $tempPassword);
    }

    public function berhasil(Request $request, User $user): View
    {
        $tempPassword = $request->session()->get('temp_password');

        abort_unless($tempPassword, 404);

        return view('manager.akun.berhasil', [
            'user' => $user,
            'tempPassword' => $tempPassword,
        ]);
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $tempPassword = $this->generateTempPassword();

        $user->update([
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('manager.akun.index', ['tab' => $user->role])
            ->with('success', "Kata sandi {$user->name} berhasil direset.")
            ->with('reset_password_for', $user->id)
            ->with('temp_password', $tempPassword);
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('manager.akun.index', ['tab' => $user->role])
            ->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    protected function generateUsername(string $name, string $role): string
    {
        $firstName = Str::of($name)->trim()->explode(' ')->first();
        $base = Str::slug($firstName, '').'.'.$role;

        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $suffix++;
            $username = $base.$suffix;
        }

        return $username;
    }

    protected function generateTempPassword(): string
    {
        // 8 karakter acak alfanumerik, mudah dibacakan (tanpa 0/O/1/l).
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';

        $password = '';
        for ($i = 0; $i < 8; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}

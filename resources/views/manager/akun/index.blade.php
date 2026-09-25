@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Kelola Akun')
@section('heroTitle', 'Kelola Akun')
@section('heroSub', 'Buat, reset kata sandi, dan aktif/nonaktifkan akun Operator serta Teknisi.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="callout">Manager dapat membuat akun Operator maupun Teknisi baru langsung dari sini. Sistem membuat username &amp; kata sandi sementara secara otomatis — tanpa perlu role Admin terpisah. Jika Operator/Teknisi lupa kata sandi, gunakan tombol <strong>Reset Kata Sandi</strong> pada akun yang bersangkutan — bukan membuat akun baru — supaya username serta seluruh riwayat laporan/tugasnya tetap utuh.</div>

@if (session('temp_password') && session('reset_password_for'))
  <div class="callout warn" style="margin-top:14px;">
    Kata sandi sementara baru: <b class="mono">{{ session('temp_password') }}</b> — sampaikan langsung ke pengguna terkait.
  </div>
@endif

<div class="account-tabs">
  <a href="{{ route('manager.akun.index', ['tab' => 'teknisi']) }}" class="acc-tab {{ $tab === 'teknisi' ? 'on' : '' }}">Teknisi <span class="tab-count">{{ $tab === 'teknisi' ? $akun->count() : \App\Models\User::where('role','teknisi')->count() }}</span></a>
  <a href="{{ route('manager.akun.index', ['tab' => 'operator']) }}" class="acc-tab {{ $tab === 'operator' ? 'on' : '' }}">Operator <span class="tab-count">{{ $tab === 'operator' ? $akun->count() : \App\Models\User::where('role','operator')->count() }}</span></a>
</div>

@if ($tab === 'teknisi')
<div class="panel">
  <div class="panel-head">
    <h3>Akun Teknisi</h3>
    <a class="btn btn-amber btn-sm" href="{{ route('manager.akun.create', ['role' => 'teknisi']) }}">+ Tambah Teknisi</a>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-scroll"><table>
      <thead><tr><th>Nama</th><th>Username</th><th>Bagian / Keahlian</th><th>Tugas Aktif</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($akun as $u)
        <tr>
          <td>{{ $u->name }}</td><td class="mono">{{ $u->username }}</td><td>{{ $u->bagian }}</td>
          <td class="mono">{{ $u->jumlah_laporan }} tugas</td>
          <td><span class="badge {{ $u->is_active ? 'b-green' : 'b-gray' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
          <td style="display:flex;gap:8px;flex-wrap:wrap;">
            <form method="POST" action="{{ route('manager.akun.resetPassword', $u) }}" onsubmit="return confirm('Reset kata sandi {{ $u->name }}?');">
              @csrf
              <button class="btn btn-outline btn-sm" type="submit">Reset Kata Sandi</button>
            </form>
            <form method="POST" action="{{ route('manager.akun.toggleStatus', $u) }}">
              @csrf
              <button class="btn btn-outline btn-sm" type="submit">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);padding:24px;">Belum ada akun teknisi.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
</div>
@else
<div class="panel">
  <div class="panel-head">
    <h3>Akun Operator</h3>
    <a class="btn btn-amber btn-sm" href="{{ route('manager.akun.create', ['role' => 'operator']) }}">+ Tambah Operator</a>
  </div>
  <div class="panel-body" style="padding:0;">
    <div class="table-scroll"><table>
      <thead><tr><th>Nama</th><th>Username</th><th>Area / Stasiun</th><th>Laporan Aktif</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($akun as $u)
        <tr>
          <td>{{ $u->name }}</td><td class="mono">{{ $u->username }}</td><td>{{ $u->bagian }}</td>
          <td class="mono">{{ $u->jumlah_laporan }} laporan</td>
          <td><span class="badge {{ $u->is_active ? 'b-green' : 'b-gray' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
          <td style="display:flex;gap:8px;flex-wrap:wrap;">
            <form method="POST" action="{{ route('manager.akun.resetPassword', $u) }}" onsubmit="return confirm('Reset kata sandi {{ $u->name }}?');">
              @csrf
              <button class="btn btn-outline btn-sm" type="submit">Reset Kata Sandi</button>
            </form>
            <form method="POST" action="{{ route('manager.akun.toggleStatus', $u) }}">
              @csrf
              <button class="btn btn-outline btn-sm" type="submit">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--ink-soft);padding:24px;">Belum ada akun operator.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
</div>
@endif
@endsection

@extends('layouts.app')

@section('crumb', 'Teknisi')
@section('pageTitle', 'Profil Saya')
@section('heroTitle', 'Profil Saya')
@section('heroSub', 'Kelola data akun dan kata sandi Anda.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Profil Saya</h3></div>
  <div class="panel-body">
    <form method="POST" action="{{ route('profil.update') }}">
      @csrf
      @method('PUT')

      @if ($errors->any())
        <div class="callout danger" style="margin-bottom:16px;">{{ $errors->first() }}</div>
      @endif

      <div class="form-grid">
        <div class="field">
          <label for="fld_NamaLengkap_20">Nama Lengkap</label>
          <input id="fld_NamaLengkap_20" type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="field">
          <label for="fld_Username_21">Username</label>
          <input id="fld_Username_21" type="text" value="{{ $user->username }}" disabled style="background:var(--disabled-bg);">
        </div>
        <div class="field">
          <label for="fld_NoHP_22">No. HP</label>
          <input id="fld_NoHP_22" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>
        <div class="field">
          <label>Bagian / Keahlian</label>
          <select name="bagian">
            @foreach ($bagianOptions as $opt)
              <option value="{{ $opt }}" {{ old('bagian', $user->bagian) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="panel-head" style="padding:0;margin-top:22px;border-bottom:none;">
        <h3 style="font-size:15px;">Ganti Kata Sandi</h3>
      </div>
      <div class="form-grid" style="margin-top:10px;">
        <div class="field">
          <label for="fld_KataSandiBaru_23">Kata Sandi Baru</label>
          <input id="fld_KataSandiBaru_23" type="password" name="kata_sandi_baru" placeholder="Minimal 8 karakter">
        </div>
        <div class="field">
          <label for="fld_KonfirmasiKataSandiBaru_24">Konfirmasi Kata Sandi Baru</label>
          <input id="fld_KonfirmasiKataSandiBaru_24" type="password" name="konfirmasi_kata_sandi_baru" placeholder="Ulangi kata sandi baru">
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Simpan Perubahan</button>
        <a class="btn btn-outline" href="{{ route('teknisi.dashboard') }}">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

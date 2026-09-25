@extends('layouts.app')

@section('crumb', 'Operator')
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
          <label for="fld_NamaLengkap_4">Nama Lengkap</label>
          <input id="fld_NamaLengkap_4" type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="field">
          <label for="fld_Username_5">Username</label>
          <input id="fld_Username_5" type="text" value="{{ $user->username }}" disabled style="background:var(--disabled-bg);">
        </div>
        <div class="field">
          <label for="fld_NoHP_6">No. HP</label>
          <input id="fld_NoHP_6" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>
        <div class="field">
          <label for="fld_BagianArea_7">Bagian / Area</label>
          <input id="fld_BagianArea_7" type="text" value="{{ $user->bagian }}" disabled style="background:var(--disabled-bg);">
          <span class="hint">Diatur oleh Manager, tidak dapat diubah sendiri</span>
        </div>
      </div>

      <div class="panel-head" style="padding:0;margin-top:22px;border-bottom:none;">
        <h3 style="font-size:15px;">Ganti Kata Sandi</h3>
      </div>
      <div class="form-grid" style="margin-top:10px;">
        <div class="field">
          <label for="fld_KataSandiBaru_8">Kata Sandi Baru</label>
          <input id="fld_KataSandiBaru_8" type="password" name="kata_sandi_baru" placeholder="Minimal 8 karakter">
        </div>
        <div class="field">
          <label for="fld_KonfirmasiKataSandiBaru_9">Konfirmasi Kata Sandi Baru</label>
          <input id="fld_KonfirmasiKataSandiBaru_9" type="password" name="konfirmasi_kata_sandi_baru" placeholder="Ulangi kata sandi baru">
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Simpan Perubahan</button>
        <a class="btn btn-outline" href="{{ route('operator.dashboard') }}">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

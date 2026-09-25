@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Akun Berhasil Dibuat')
@section('heroTitle', 'Akun Berhasil Dibuat')
@section('heroSub', 'Sampaikan username &amp; kata sandi sementara ini kepada pengguna terkait.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head">
    <h3>Akun {{ $user->roleLabel() }} Berhasil Dibuat</h3>
    <span class="badge b-green">Aktif</span>
  </div>
  <div class="panel-body">
    <div class="detail-grid">
      <div>
        <div class="kv"><span class="k">Nama</span><span class="v">{{ $user->name }}</span></div>
        <div class="kv"><span class="k">{{ $user->isTeknisi() ? 'Bagian / Keahlian' : 'Area / Stasiun' }}</span><span class="v">{{ $user->bagian }}</span></div>
      </div>
      <div>
        <div class="kv"><span class="k">Username</span><span class="v mono">{{ $user->username }}</span></div>
        <div class="kv"><span class="k">Kata Sandi Sementara</span><span class="v mono">{{ $tempPassword }}</span></div>
      </div>
    </div>
    <div class="callout warn" style="margin-top:16px;">Sampaikan username &amp; kata sandi sementara ini ke {{ $user->roleLabel() }} secara langsung. {{ $user->roleLabel() }} akan diminta membuat kata sandi baru saat login pertama.</div>
    <div class="action-bar">
      <a class="btn btn-amber" href="{{ route('manager.akun.index', ['tab' => $user->role]) }}">Kembali ke Daftar Akun</a>
    </div>
  </div>
</div>
@endsection

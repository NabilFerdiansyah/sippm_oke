@extends('layouts.app')

@section('crumb', 'Manager')
@section('pageTitle', 'Tambah Akun Baru')
@section('heroTitle', 'Tambah Akun Baru')
@section('heroSub', 'Username &amp; kata sandi sementara akan dibuat otomatis oleh sistem.')
@section('heroBadge', auth()->user()->roleSubLabel())

@section('content')
<div class="panel">
  <div class="panel-head"><h3>Tambah Akun Baru</h3></div>
  <div class="panel-body">
    @if ($errors->any())
      <div class="callout danger">{{ $errors->first() }}</div>
    @endif
    <div class="acc-role-toggle">
      <a href="{{ route('manager.akun.create', ['role' => 'operator']) }}" class="acc-role-opt {{ $role === 'operator' ? 'on' : '' }}">Operator</a>
      <a href="{{ route('manager.akun.create', ['role' => 'teknisi']) }}" class="acc-role-opt {{ $role === 'teknisi' ? 'on' : '' }}">Teknisi</a>
    </div>
    <form method="POST" action="{{ route('manager.akun.store') }}">
      @csrf
      <input type="hidden" name="role" value="{{ $role }}">
      <div class="form-grid">
        <div class="field">
          <label>Nama Lengkap</label>
          <input type="text" name="name" id="newAccNama" value="{{ old('name') }}" placeholder="mis. Wahyu Setiawan" oninput="syncUsernamePreview()" required>
        </div>
        <div class="field">
          <label>No. HP</label>
          <input type="text" name="phone" id="newAccHp" value="{{ old('phone') }}" placeholder="mis. 0812xxxxxxx">
        </div>
        <div class="field">
          <label id="newAccBagianLabel">{{ $role === 'teknisi' ? 'Bagian / Keahlian' : 'Area / Stasiun' }}</label>
          <select name="bagian" id="newAccBagian" required>
            @foreach ($bagianOptions as $opt)
              <option value="{{ $opt }}" {{ old('bagian') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Username</label>
          <input type="text" id="newAccUsername" value="{{ \Illuminate\Support\Str::slug(old('name', 'user'), '') }}.{{ $role }}" disabled style="background:var(--disabled-bg);">
          <span class="hint">Dibuat otomatis dari nama &amp; peran yang dipilih</span>
        </div>
      </div>
      <div class="callout" style="margin-top:16px;margin-bottom:0;" id="newAccCallout">Kata sandi sementara akan digenerate otomatis oleh sistem dan wajib diganti oleh {{ $role === 'teknisi' ? 'Teknisi' : 'Operator' }} saat login pertama kali.</div>
      <div class="action-bar">
        <button class="btn btn-amber" type="submit">Buat Akun</button>
        <a class="btn btn-outline" href="{{ route('manager.akun.index', ['tab' => $role]) }}">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function syncUsernamePreview(){
    const nama = document.getElementById('newAccNama').value.trim().split(/\s+/)[0] || 'user';
    const slug = nama.toLowerCase().replace(/[^a-z0-9]/g, '');
    document.getElementById('newAccUsername').value = (slug || 'user') + '.{{ $role }}';
  }
</script>
@endsection

@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('topbar-title', 'Log Aktivitas')
@section('topbar-subtitle', 'Riwayat rekam jejak aktivitas perubahan data')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/>
  </svg>
@endsection

@section('content')
<div class="content-header">
  <div>
    <h2 class="content-title">Riwayat Aktivitas Admin</h2>
    <p class="content-subtitle">Mencatat semua perubahan (Tambah, Ubah, Hapus) pada data transaksi oleh admin.</p>
  </div>
</div>

<div class="card" style="margin-bottom: 20px; padding: 16px 20px; border-radius: 12px;">
  <form method="GET" action="{{ route('logs.index') }}" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 240px;">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama admin, aksi, atau keterangan detail..." class="form-control" style="width: 100%; padding: 9px 14px; border-radius: 8px;">
    </div>
    <div>
      <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control" style="padding: 9px 14px; border-radius: 8px;">
    </div>
    <button type="submit" class="btn btn-primary" style="padding: 9px 18px; border-radius: 8px;">Filter</button>
    @if(request()->hasAny(['search', 'tanggal']))
      <a href="{{ route('logs.index') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 13px; font-weight: 600; padding: 9px 14px;">Reset</a>
    @endif
  </form>
</div>

<div class="card">
  <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
    <table class="table" style="min-width:640px;">
      <thead>
        <tr>
          <th>WAKTU</th>
          <th>NAMA ADMIN</th>
          <th>AKSI</th>
          <th>KETERANGAN DETAIL</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-size: 13px; color: var(--text-secondary); white-space: nowrap;">
            {{ $log->created_at->format('d M Y, H:i') }}
          </td>
          <td>
            <div style="display:flex; align-items:center; gap:8px">
              @if($log->user && $log->user->avatar)
                <img src="{{ $log->user->avatar }}" alt="Avatar" style="width:24px; height:24px; border-radius:50%; object-fit:cover; border: 1px solid var(--accent);" />
              @else
                <div style="width:24px; height:24px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:11px">
                  {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                </div>
              @endif
              <strong style="font-size: 13px;">{{ $log->user ? $log->user->name : 'Sistem' }}</strong>
            </div>
          </td>
          <td>
            @php
              $bgColor = 'rgba(107,114,128,0.1)';
              $textColor = '#9ca3af';
              if (str_contains(strtolower($log->action), 'tambah')) {
                  $bgColor = 'rgba(16,185,129,0.12)';
                  $textColor = '#34d399';
              } elseif (str_contains(strtolower($log->action), 'ubah')) {
                  $bgColor = 'rgba(59,130,246,0.12)';
                  $textColor = '#60a5fa';
              } elseif (str_contains(strtolower($log->action), 'hapus')) {
                  $bgColor = 'rgba(239,68,68,0.12)';
                  $textColor = '#f87171';
              }
            @endphp
            <span style="font-size:11px; font-weight:600; padding:4px 8px; border-radius:4px; background:{{ $bgColor }}; color:{{ $textColor }};">
              {{ $log->action }}
            </span>
          </td>
          <td style="font-size: 13px; color: var(--text-primary);">
            {{ $log->description }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align:center; padding: 30px; color: var(--text-muted);">
            Belum ada log aktivitas tercatat.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div style="margin-top: 16px;">
  @if($logs->hasPages())
  <div class="pagination-wrap" style="background:var(--bg-card);border-radius:10px;border:1px solid var(--border);">
    <div class="pagination-info">
      Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
    </div>
    <div class="pagination-nav">
      @if($logs->onFirstPage())
        <span class="page-btn page-btn-disabled">‹</span>
      @else
        <a href="{{ $logs->previousPageUrl() }}" class="page-btn">‹</a>
      @endif

      @php
        $current = $logs->currentPage();
        $last    = $logs->lastPage();
        $start   = max(1, $current - 2);
        $end     = min($last, $current + 2);
      @endphp

      @if($start > 1)
        <a href="{{ $logs->url(1) }}" class="page-btn">1</a>
        @if($start > 2)<span class="page-ellipsis">…</span>@endif
      @endif
      @for($p = $start; $p <= $end; $p++)
        @if($p === $current)
          <span class="page-btn page-btn-active">{{ $p }}</span>
        @else
          <a href="{{ $logs->url($p) }}" class="page-btn">{{ $p }}</a>
        @endif
      @endfor
      @if($end < $last)
        @if($end < $last - 1)<span class="page-ellipsis">…</span>@endif
        <a href="{{ $logs->url($last) }}" class="page-btn">{{ $last }}</a>
      @endif

      @if($logs->hasMorePages())
        <a href="{{ $logs->nextPageUrl() }}" class="page-btn">›</a>
      @else
        <span class="page-btn page-btn-disabled">›</span>
      @endif
    </div>
  </div>
  @endif
</div>
@endsection

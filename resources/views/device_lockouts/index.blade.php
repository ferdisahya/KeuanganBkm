@extends('layouts.app')
@section('title', 'Keamanan Device & Blokir')
@section('topbar-title', 'Keamanan Device / Blokir')
@section('topbar-subtitle', 'Manajemen pemblokiran IP Device – Project Manager, Direktur Operasional & Direktur')
@section('topbar-icon')
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
  </svg>
@endsection

@section('content')
<div class="content-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px;">
  <div>
    <h2 class="content-title" style="font-size: 28px; font-weight: 800; letter-spacing: -0.5px; color: var(--text-primary);">Daftar Device / IP Diblokir</h2>
    <p class="content-subtitle" style="font-size: 14px; color: var(--text-secondary); margin-top: 6px;">Kelola status pemblokiran IP Device akibat percobaan login gagal. Anda dapat membuka blokir kapan saja.</p>
  </div>
</div>

<div class="card" style="margin-bottom: 24px; padding: 16px 24px; border-radius: 12px;">
  <form method="GET" action="{{ route('device_lockouts.index') }}" style="display: flex; gap: 12px; align-items: center;">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari IP Address atau alasan blokir..." class="form-control" style="flex: 1; padding: 10px 14px; border-radius: 8px;">
    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 8px;">Cari</button>
    @if(request()->filled('search'))
      <a href="{{ route('device_lockouts.index') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 13px; font-weight: 600; padding: 10px 14px;">Reset</a>
    @endif
  </form>
</div>

<div class="card" style="margin-bottom: 24px; padding: 20px 24px; background: rgba(225, 29, 72, 0.05); border: 1px solid rgba(225, 29, 72, 0.15); border-radius: 12px; display: flex; gap: 16px; align-items: flex-start;">
  <div style="background: rgba(225, 29, 72, 0.1); padding: 10px; border-radius: 10px; color: #e11d48;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  </div>
  <div>
    <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 700; color: var(--text-primary);">Aturan Pemblokiran Device otomatis</h4>
    <p style="margin: 0; font-size: 13.5px; color: var(--text-secondary); line-height: 1.6;">
      • <strong>3 Kali Gagal Login</strong>: IP Device akan diblokir otomatis selama <strong>3 Jam</strong>.<br>
      • <strong>Total 5 Kali Gagal Login</strong>: IP Device akan diblokir otomatis selama <strong>5 Hari</strong>.<br>
      • Sebagai <strong>{{ auth()->user()->role }}</strong>, Anda memiliki wewenang penuh untuk membuka blokir (*Unblock*) device kapan pun dibutuhkan.
    </p>
  </div>
</div>

@if($lockouts->isEmpty())
<div class="card" style="padding: 60px 40px; text-align: center; border-radius: 16px; border: 1px dashed var(--border);">
  <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 11 13 15 9"/></svg>
  </div>
  <h3 style="font-size: 18px; color: var(--text-primary); margin-bottom: 8px;">Tidak Ada Device yang Diblokir</h3>
  <p style="color: var(--text-muted); font-size: 14px; margin: 0; max-width: 400px; margin-inline: auto; line-height: 1.5;">
    Seluruh device IP dalam keadaan normal. Jika ada percobaan login ilegal yang gagal 3x atau 5x, riwayat IP akan otomatis tercatat di sini.
  </p>
</div>
@else
<div class="card" style="border-radius: 16px; overflow: hidden; padding: 0;">
  <div class="table-responsive" style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
    <table class="table" style="min-width: 900px; border-collapse: separate; border-spacing: 0;">
      <thead>
        <tr style="background: var(--bg-surface);">
          <th style="width: 60px; padding: 16px 24px; border-bottom: 1px solid var(--border);">NO</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">IP DEVICE</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">STATUS BLOKIR</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">ATTEMPTS</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">DIBLOKIR HINGGA</th>
          <th style="padding: 16px 24px; border-bottom: 1px solid var(--border);">ALASAN</th>
          <th style="width: 160px; text-align: right; padding: 16px 24px; border-bottom: 1px solid var(--border);">AKSI</th>
        </tr>
      </thead>
      <tbody>
        @foreach($lockouts as $index => $l)
        @php
          $isBlocked = $l->isCurrentlyBlocked();
        @endphp
        <tr style="transition: all 0.2s ease;">
          <td style="padding: 20px 24px; font-weight: 600; color: var(--text-secondary);">{{ $index + 1 }}</td>
          <td style="padding: 20px 24px;">
            <div style="display:flex; align-items:center; gap:10px">
              <span style="font-family: monospace; font-size: 14px; font-weight: 700; color: var(--text-primary); background: var(--bg-surface); padding: 4px 8px; border-radius: 6px; border: 1px solid var(--border);">
                {{ $l->ip_address }}
              </span>
            </div>
          </td>
          <td style="padding: 20px 24px;">
            @if($isBlocked)
              <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; background: rgba(225,29,72,0.15); border: 1px solid rgba(225,29,72,0.3); color: #f43f5e;">
                <span style="width:8px; height:8px; border-radius:50%; background:#f43f5e; display:inline-block;"></span>
                TERBLOKIR
              </span>
            @elseif($l->unblocked_at)
              <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 20px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981;">
                Dibuka oleh {{ $l->unblocker->name ?? 'PM' }}
              </span>
            @else
              <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 20px; background: var(--bg-surface); color: var(--text-secondary);">
                Normal
              </span>
            @endif
          </td>
          <td style="padding: 20px 24px; font-weight: 700; font-size: 14px; color: var(--text-primary);">
            {{ $l->failed_attempts }}x Gagal
          </td>
          <td style="padding: 20px 24px; font-size: 13px; color: var(--text-secondary);">
            @if($l->blocked_until && $isBlocked)
              <strong>{{ $l->blocked_until->format('d M Y H:i') }}</strong>
              <div style="font-size: 11px; color: #f43f5e; margin-top: 2px;">
                Sisa: {{ $l->blocked_until->diffForHumans(['parts' => 2, 'join' => true, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
              </div>
            @else
              -
            @endif
          </td>
          <td style="padding: 20px 24px; font-size: 13px; color: var(--text-secondary);">
            {{ $l->block_reason ?? '-' }}
          </td>
          <td style="padding: 20px 24px; text-align: right;">
            @if($isBlocked)
              <form method="POST" action="{{ route('device_lockouts.unblock', $l->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membuka blokir IP {{ $l->ip_address }}?');" style="display: inline-block;">
                @csrf
                <button type="submit" class="btn" style="padding: 8px 16px; font-size: 12.5px; font-weight: 700; border-radius: 8px; background: #10b981; color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 10px rgba(16,185,129,0.3);">
                  Buka Blokir
                </button>
              </form>
            @else
              <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Tidak diblokir</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Form Ajukan Dana Gaji')
@section('topbar-title', 'Form Ajukan Dana Gaji')
@section('topbar-subtitle', 'Pencatatan pengajuan dana formal Gaji dengan approval berjenjang & bukti transfer')

@section('content')
<style>
  .dashboard-welcome-card {
    background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%);
    border-radius: 16px;
    padding: 24px 30px;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(122, 15, 29, 0.3);
  }
  .dashboard-welcome-card::after {
    content: '';
    position: absolute;
    right: -40px;
    bottom: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(212, 160, 23, 0.25) 0%, rgba(212, 160, 23, 0) 70%);
    border-radius: 50%;
  }
  .dashboard-welcome-text h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 24px;
    font-weight: 800;
    margin: 4px 0;
  }
  .dashboard-welcome-text p {
    font-size: 13.5px;
    opacity: 0.85;
    margin: 0;
    font-weight: 400;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }
  .stat-card-custom {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
  }
  .stat-card-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .filter-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
  }
  .table-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }
  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .data-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.05em;
    padding: 14px 16px;
    border-bottom: 1.5px solid #e2e8f0;
    text-align: left;
  }
  .data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }
  .data-table tr:hover {
    background: #f8fafc;
  }
  .btn-primary-maroon {
    background: linear-gradient(135deg, #7a0f1d 0%, #5a0712 100%);
    color: #ffffff;
    padding: 10px 20px;
    font-weight: 700;
    border-radius: 10px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(122, 15, 29, 0.2);
    transition: all 0.2s;
  }
  .btn-primary-maroon:hover {
    color: #ffffff;
    opacity: 0.95;
    transform: translateY(-1px);
  }
  .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.02em;
  }
  .badge-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
  }
  .badge-approved {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
  }
  .badge-rejected {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
  }
  .approval-steps {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;
  }
  .step-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .step-done {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
  }
  .step-waiting {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
  }
  .step-rejected {
    background: #ffe4e6;
    color: #be123c;
    border: 1px solid #fecdd3;
  }
  .form-control-custom {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
  }
  .form-control-custom:focus {
    border-color: #7a0f1d;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(122, 15, 29, 0.1);
  }
  .icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
  }
  .modal-backdrop-custom {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 999999;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
</style>

{{-- Flash Messages --}}
@if(session('success'))
  <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; justify-content: space-between;">
    <span>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()" style="background:none; border:none; font-size:16px; cursor:pointer; color:#065f46;">✕</button>
  </div>
@endif

@if(session('error'))
  <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; justify-content: space-between;">
    <span>{{ session('error') }}</span>
    <button onclick="this.parentElement.remove()" style="background:none; border:none; font-size:16px; cursor:pointer; color:#991b1b;">✕</button>
  </div>
@endif

{{-- Header Card --}}
<div class="dashboard-welcome-card">
  <div class="dashboard-welcome-text" style="z-index: 2;">
    <p>Menu Utama</p>
    <h1>Form Ajukan Dana Gaji</h1>
    <p>Pengajuan dana khusus Gaji dengan alur persetujuan berjenjang & pengunggahan bukti transfer</p>
  </div>
  <div style="z-index: 2;">
    <button type="button" class="btn-primary-maroon" onclick="openCreateModal()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Ajukan Dana Gaji
    </button>
  </div>
</div>

{{-- Stat Cards --}}
<div class="stats-grid">
  <div class="stat-card-custom">
    <div class="stat-card-icon" style="background: rgba(122, 15, 29, 0.1); color: #7a0f1d;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Pengajuan</div>
      <div style="font-size: 22px; font-weight: 800; color: #1e293b; font-family: 'Outfit', sans-serif;">{{ $stats['total_pengajuan'] }}</div>
    </div>
  </div>

  <div class="stat-card-custom">
    <div class="stat-card-icon" style="background: rgba(22, 163, 74, 0.1); color: #16a34a;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Disetujui Penuh (Rp)</div>
      <div style="font-size: 20px; font-weight: 800; color: #16a34a; font-family: 'Outfit', sans-serif;">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</div>
    </div>
  </div>

  <div class="stat-card-custom">
    <div class="stat-card-icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Menunggu Approval</div>
      <div style="font-size: 22px; font-weight: 800; color: #d97706; font-family: 'Outfit', sans-serif;">{{ $stats['pending_count'] }}</div>
    </div>
  </div>

  <div class="stat-card-custom">
    <div class="stat-card-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div>
      <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Approved All</div>
      <div style="font-size: 22px; font-weight: 800; color: #2563eb; font-family: 'Outfit', sans-serif;">{{ $stats['approved_all_count'] }}</div>
    </div>
  </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
  <form action="{{ route('form_ajukan_dana_gaji.index') }}" method="GET">
    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
      <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; flex: 1;">
        <select name="status" class="form-control-custom" style="width: 160px;">
          <option value="semua">-- Semua Status --</option>
          <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
          <option value="Approved All" {{ request('status') === 'Approved All' ? 'selected' : '' }}>Approved All</option>
          <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>

        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control-custom" style="width: 150px;" title="Dari Tanggal">
        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control-custom" style="width: 150px;" title="Sampai Tanggal">

        <button type="submit" class="btn-primary-maroon" style="padding: 10px 16px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Filter
        </button>

        @if(request()->hasAny(['status', 'tanggal_mulai', 'tanggal_selesai', 'search']))
          <a href="{{ route('form_ajukan_dana_gaji.index') }}" class="btn-primary-maroon" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; box-shadow: none; padding: 10px 14px;" title="Reset Filter">
            Reset
          </a>
        @endif
      </div>

      <div style="position: relative; width: 260px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan, nominal..." class="form-control-custom" style="width: 100%; padding-left: 36px;">
        <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
    </div>
  </form>
</div>

{{-- Table Card --}}
<div class="table-card">
  <div style="overflow-x: auto;">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 50px;">No</th>
          <th style="width: 120px;">Tanggal</th>
          <th>Diajukan Oleh</th>
          <th style="text-align: right; width: 170px;">Nominal (Rp)</th>
          <th style="text-align: center; width: 120px;">File PDF</th>
          <th style="width: 220px;">Status &amp; Approval</th>
          <th style="text-align: center;">Bukti Transfer</th>
          <th style="text-align: center; width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengajuans as $index => $row)
          <tr>
            <td style="font-weight: 600; color: #94a3b8;">{{ $pengajuans->firstItem() + $index }}</td>
            <td style="font-weight: 600; white-space: nowrap;">
              {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
            </td>
            <td>
              <div style="font-weight: 700; color: #1e293b;">{{ $row->user->name ?? 'Admin Keuangan' }}</div>
              @if($row->keterangan)
                <div style="font-size: 11.5px; color: #64748b; max-width: 220px;" title="{{ $row->keterangan }}">
                  {{ Str::limit($row->keterangan, 40) }}
                </div>
              @endif
            </td>
            <td style="text-align: right; font-weight: 800; color: #7a0f1d; white-space: nowrap; font-size: 14px;">
              Rp {{ number_format($row->nominal, 0, ',', '.') }}
            </td>
            <td style="text-align: center; white-space: nowrap;">
              <a href="{{ asset($row->file) }}" target="_blank" class="icon-btn" style="background: rgba(122, 15, 29, 0.1); color: #7a0f1d; display: inline-flex; width: auto; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; gap: 6px;" title="Buka PDF Pengajuan">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                PDF Pengajuan
              </a>
            </td>
            <td>
              @if($row->status === 'Approved All')
                <span class="badge-status badge-approved">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  Approved All
                </span>
              @elseif($row->status === 'Ditolak')
                <span class="badge-status badge-rejected" title="{{ $row->rejection_reason }}">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  Ditolak
                </span>
                @if($row->rejection_reason)
                  <div style="font-size: 11px; color: #dc2626; margin-top: 4px; font-weight: 500;">
                    Alasan: {{ $row->rejection_reason }}
                  </div>
                @endif
              @else
                <span class="badge-status badge-pending">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Pending
                </span>
              @endif

              {{-- Approval Steps Indicators --}}
              <div class="approval-steps">
                @if($row->isPmApproved())
                  <span class="step-pill step-done" title="Disetujui PM: {{ $row->pmUser->name ?? '-' }} ({{ $row->pm_approved_at ? $row->pm_approved_at->format('d/m H:i') : '' }})">
                    ✓ PM: Selesai
                  </span>
                @else
                  <span class="step-pill step-waiting">○ PM: Menunggu</span>
                @endif

                @if($row->needsDoApproval())
                  @if($row->isDoApproved())
                    <span class="step-pill step-done" title="Disetujui DO: {{ $row->doUser->name ?? '-' }} ({{ $row->do_approved_at ? $row->do_approved_at->format('d/m H:i') : '' }})">
                      ✓ Dir Ops: Selesai
                    </span>
                  @else
                    <span class="step-pill step-waiting">○ Dir Ops: Menunggu</span>
                  @endif
                @endif

                @if($row->needsDirApproval())
                  @if($row->isDirApproved())
                    <span class="step-pill step-done" title="Disetujui Direktur: {{ $row->dirUser->name ?? '-' }} ({{ $row->dir_approved_at ? $row->dir_approved_at->format('d/m H:i') : '' }})">
                      ✓ Direktur: Selesai
                    </span>
                  @else
                    <span class="step-pill step-waiting">○ Direktur: Menunggu</span>
                  @endif
                @endif
              </div>
            </td>

            {{-- Bukti Transfer --}}
            <td style="text-align: center; white-space: nowrap;">
              @if($row->bukti)
                <a href="{{ asset($row->bukti) }}" target="_blank" class="icon-btn" style="background: rgba(22, 163, 74, 0.1); color: #16a34a; display: inline-flex; width: auto; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; gap: 6px;" title="Lihat Bukti Transfer">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                  Lihat Bukti
                </a>
              @elseif($row->status === 'Approved All')
                <span style="font-size: 11.5px; color: #ea580c; font-weight: 600; background: #fff7ed; padding: 4px 8px; border-radius: 6px; border: 1px solid #ffedd5;">
                  Belum Diunggah
                </span>
              @else
                <span style="color: #94a3b8; font-size: 12px;">-</span>
              @endif
            </td>

            {{-- Aksi --}}
            <td style="text-align: center; white-space: nowrap;">
              <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                {{-- Approval Button --}}
                @if($row->canUserApprove(auth()->user()))
                  <form action="{{ route('form_ajukan_dana_gaji.approve', $row->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan dana ini?')">
                    @csrf
                    <button type="submit" class="icon-btn" style="background: rgba(22, 163, 74, 0.12); color: #16a34a;" title="Setujui Pengajuan">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                  </form>
                @endif

                {{-- Reject Button --}}
                @if($row->canUserReject(auth()->user()))
                  <button type="button" class="icon-btn" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;" title="Tolak Pengajuan" onclick="openRejectModal({{ $row->id }}, '{{ number_format($row->nominal, 0, ',', '.') }}')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                @endif

                {{-- Upload Bukti Transfer Button --}}
                @if($row->status === 'Approved All')
                  <button type="button" class="icon-btn" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;" title="Unggah Bukti Transfer" onclick="openBuktiModal({{ $row->id }})">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                  </button>
                @endif

                {{-- Delete Button --}}
                @if($row->status !== 'Approved All' || auth()->user()->canManageUsersAndSecurity())
                  <button type="button" class="icon-btn" style="background: rgba(148, 163, 184, 0.15); color: #64748b;" title="Hapus Pengajuan"
                          data-url="{{ route('form_ajukan_dana_gaji.destroy', $row->id) }}"
                          data-title="Hapus Form Ajukan Dana Gaji?"
                          data-name="Pengajuan Rp {{ number_format($row->nominal, 0, ',', '.') }} ({{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }})"
                          data-type="pengajuan dana Gaji"
                          onclick="confirmDelete(this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 500;">
              Belum ada data pengajuan dana Gaji.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($pengajuans->hasPages())
    <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
      {{ $pengajuans->links() }}
    </div>
  @endif
</div>

{{-- MODAL: Ajukan Dana Gaji Baru --}}
<div id="createModal" class="modal-backdrop-custom">
  <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 580px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #7a0f1d 0%, #4a030a 100%); color: #ffffff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
      <h5 style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Form Ajukan Dana Gaji Baru
      </h5>
      <button type="button" onclick="closeCreateModal()" style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; font-weight: bold;">✕</button>
    </div>

    <form action="{{ route('form_ajukan_dana_gaji.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="padding: 24px; max-height: calc(100vh - 200px); overflow-y: auto;">
        {{-- Aturan Approval Info Box --}}
        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 12px; color: #92400e;">
          <div style="font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Tingkatan Approval Berdasarkan Nominal:
          </div>
          <ul style="margin: 0; padding-left: 18px; line-height: 1.5;">
            <li><strong>&lt; Rp 2.000.000:</strong> Persetujuan Project Manager.</li>
            <li><strong>Rp 2.000.000 s/d Rp 10.000.000:</strong> Persetujuan Project Manager &amp; Direktur Operasional.</li>
            <li><strong>&gt; Rp 10.000.000:</strong> Persetujuan Project Manager, Direktur Operasional, &amp; Direktur.</li>
          </ul>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Tanggal Pengajuan (Otomatis Hari Ini)</label>
          <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control-custom" style="width: 100%;" required>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
            Nominal Dana yang Diajukan (Rp) <span style="color: #dc2626;">*</span>
          </label>
          <input type="text" id="nominalInputOps" name="nominal" placeholder="Contoh: 5.000.000" class="form-control-custom rupiah-input" style="width: 100%; font-size: 16px; font-weight: 700; color: #7a0f1d;" required oninput="formatRupiahInput(this)">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
            Upload File Dokumen PDF Pengajuan <span style="color: #dc2626;">*</span>
          </label>
          <input type="file" name="file" accept=".pdf" class="form-control-custom" style="width: 100%; padding: 8px 12px;" required>
          <div style="font-size: 11px; color: #64748b; margin-top: 4px;">* File harus berupa dokumen PDF (maksimal 25 MB). Dokumen akan tersimpan di server <code>upload/pengajuan</code>.</div>
        </div>

        <div style="margin-bottom: 10px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Keterangan / Catatan Tambahan (Opsional)</label>
          <textarea name="keterangan" rows="3" placeholder="Tulis rincian atau keterangan pengajuan dana..." class="form-control-custom" style="width: 100%; resize: vertical;"></textarea>
        </div>
      </div>

      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
        <button type="button" onclick="closeCreateModal()" style="background: #ffffff; border: 1.5px solid #cbd5e1; color: #475569; font-weight: 600; padding: 9px 18px; border-radius: 8px; font-size: 13px; cursor: pointer;">Batal</button>
        <button type="submit" class="btn-primary-maroon">
          Kirim Pengajuan Dana
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL: Unggah Bukti Transfer --}}
<div id="buktiModal" class="modal-backdrop-custom">
  <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #7a0f1d 0%, #5a0712 100%); color: #ffffff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
      <h5 style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Unggah Bukti Transfer Gaji
      </h5>
      <button type="button" onclick="closeBuktiModal()" style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <form id="formUploadBukti" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="padding: 24px;">
        <div style="font-size: 12.5px; color: #475569; margin-bottom: 16px;">
          Pengajuan dana ini telah <strong>Approved All</strong>. Silakan unggah bukti transfer perbankan / pembayaran.
        </div>

        <div style="margin-bottom: 14px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
            Tanggal Pembayaran <span style="color: #dc2626;">*</span>
          </label>
          <input type="date" name="tanggal_bayar" class="form-control-custom" style="width: 100%; padding: 8px 12px;" required>
        </div>

        <div style="margin-bottom: 14px;">
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
            Pilih Berkas Bukti Transfer (PDF / Gambar) <span style="color: #dc2626;">*</span>
          </label>
          <input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png,.webp" class="form-control-custom" style="width: 100%; padding: 8px 12px;" required>
          <div style="font-size: 11px; color: #64748b; margin-top: 4px;">* Format diperbolehkan: PDF, JPG, PNG, WEBP (maksimal 20 MB). Gaji selalu dipotong dari Kas BKM.</div>
        </div>
      </div>

      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeBuktiModal()" style="background: #fff; border: 1.5px solid #cbd5e1; color: #475569; font-weight: 600; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;">Batal</button>
        <button type="submit" style="background: #7a0f1d; color: #fff; border: none; font-weight: 700; padding: 8px 18px; border-radius: 8px; font-size: 13px; cursor: pointer;">
          Simpan Bukti
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL: Tolak Pengajuan --}}
<div id="rejectModal" class="modal-backdrop-custom">
  <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
      <h5 style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Tolak Pengajuan Dana Gaji
      </h5>
      <button type="button" onclick="closeRejectModal()" style="background: rgba(255,255,255,0.2); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">✕</button>
    </div>

    <form id="formReject" method="POST">
      @csrf
      <div style="padding: 24px;">
        <div style="font-size: 12.5px; color: #475569; margin-bottom: 14px;">
          Anda akan menolak pengajuan dana sebesar <strong id="rejectNominalText" style="color: #dc2626;"></strong>.
        </div>

        <div>
          <label style="font-size: 12px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">
            Alasan Penolakan <span style="color: #dc2626;">*</span>
          </label>
          <textarea name="rejection_reason" rows="3" placeholder="Contoh: Dokumen lampiran kurang lengkap / anggaran tidak disetujui" class="form-control-custom" style="width: 100%; resize: vertical;" required></textarea>
        </div>
      </div>

      <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeRejectModal()" style="background: #fff; border: 1.5px solid #cbd5e1; color: #475569; font-weight: 600; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;">Batal</button>
        <button type="submit" style="background: #dc2626; color: #fff; border: none; font-weight: 700; padding: 8px 18px; border-radius: 8px; font-size: 13px; cursor: pointer;">
          Konfirmasi Tolak
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function formatRupiahInput(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
      input.value = new Intl.NumberFormat('id-ID').format(value);
    } else {
      input.value = '';
    }
  }

  function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
  }
  function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
  }

  function openBuktiModal(id) {
    const form = document.getElementById('formUploadBukti');
    form.action = `/form-ajukan-dana-gaji/${id}/upload-bukti`;
    document.getElementById('buktiModal').style.display = 'flex';
  }
  function closeBuktiModal() {
    document.getElementById('buktiModal').style.display = 'none';
  }

  function openRejectModal(id, nominalText) {
    const form = document.getElementById('formReject');
    form.action = `/form-ajukan-dana-diluar-operasional/${id}/reject`;
    document.getElementById('rejectNominalText').textContent = 'Rp ' + nominalText;
    document.getElementById('rejectModal').style.display = 'flex';
  }
  function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
  }

  window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop-custom')) {
      e.target.style.display = 'none';
    }
  });
</script>
@endsection

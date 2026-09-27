<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Dashboard') – Sistem Keuangan PT BKM</title>
  <meta name="description" content="Sistem Manajemen Keuangan PT BKM – Input transaksi, riwayat, dan laporan keuangan harian, bulanan, tahunan." />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Website Tab Favicon / Logo -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo_pt_bkm.png') }}" />
  <link rel="shortcut icon" href="{{ asset('images/logo_pt_bkm.png') }}" />
  
  <!-- Preload fonts for instant layout loading -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <script>
    (function() {
      // Force light mode to render the clean corporate white/grey theme
      document.documentElement.classList.add('theme-light');
      document.documentElement.classList.remove('theme-dark');
    })();
  </script>
  
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  
  <style>
    /* ── Layout Overrides to Match Spec Design ── */
    :root {
      --layout-primary-maroon: #7a0f1d;
      --layout-dark-maroon: #3d030a;
      --layout-gold: #dfbb6d;
      --layout-light-bg: #f8f9fa;
    }

    body {
      font-family: 'Inter', sans-serif !important;
      background-color: var(--layout-light-bg) !important;
    }

    /* ── Pagination Overrides & SVG Size Safety ── */
    .pagination svg, nav[role="navigation"] svg, .custom-pagination-wrap svg {
      width: 14px !important;
      height: 14px !important;
      max-width: 14px !important;
      max-height: 14px !important;
      display: inline-block !important;
      vertical-align: middle !important;
    }

    /* Sidebar Styling */
    .sidebar {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 250px !important;
      height: 100vh !important;
      background: linear-gradient(180deg, var(--layout-primary-maroon) 0%, var(--layout-dark-maroon) 100%) !important;
      border-right: none !important;
      display: flex !important;
      flex-direction: column !important;
      z-index: 200 !important;
      overflow: hidden !important;
    }

    .sidebar-header {
      background: rgba(0, 0, 0, 0.25) !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      margin: 16px 14px 8px 14px !important;
      border-radius: 14px !important;
      padding: 12px 14px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15) !important;
    }

    .logo-wrap {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
    }

    .sidebar-logo-img {
      width: 44px !important;
      height: 44px !important;
      object-fit: contain !important;
      flex-shrink: 0 !important;
      filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.4)) !important;
    }

    .logo-title {
      font-family: 'Outfit', sans-serif !important;
      font-size: 15px !important;
      font-weight: 700 !important;
      color: #ffffff !important;
      line-height: 1.2 !important;
    }

    .logo-sub {
      font-size: 11px !important;
      color: rgba(255, 255, 255, 0.7) !important;
      margin-top: 1px !important;
    }

    .sidebar-close {
      color: rgba(255, 255, 255, 0.6) !important;
      border-radius: 8px !important;
      padding: 4px 8px !important;
      font-size: 16px !important;
      transition: background 0.2s ease !important;
    }

    .sidebar-close:hover {
      background: rgba(255, 255, 255, 0.08) !important;
      color: #ffffff !important;
    }

    /* Sidebar Navigation (Spans full width for block item background) */
    .sidebar-nav {
      flex: 1 !important;
      overflow-y: auto !important;
      padding: 12px 0 !important;
      scrollbar-width: thin;
      scrollbar-color: rgba(223, 187, 109, 0.6) rgba(0, 0, 0, 0.2);
    }

    /* Custom thin gold-tinted scrollbar for professional look */
    .sidebar-nav::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar-nav::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.15);
      border-radius: 10px;
    }
    .sidebar-nav::-webkit-scrollbar-thumb {
      background: rgba(223, 187, 109, 0.5);
      border-radius: 10px;
    }
    .sidebar-nav::-webkit-scrollbar-thumb:hover {
      background: rgba(223, 187, 109, 0.8);
    }



    .nav-section-label {
      font-size: 10px !important;
      font-weight: 700 !important;
      letter-spacing: 0.1em !important;
      color: var(--layout-gold) !important;
      opacity: 0.85 !important;
      padding: 16px 24px 8px 24px !important;
      text-transform: uppercase !important;
    }

    /* Capsule style sidebar buttons matching the exact visual spec */
    .nav-item {
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
      margin: 4px 16px !important;
      padding: 10px 16px !important;
      border-radius: 12px !important;
      color: rgba(255, 255, 255, 0.8) !important;
      font-size: 13.5px !important;
      font-weight: 500 !important;
      text-decoration: none !important;
      transition: all 0.2s ease !important;
      border-left: none !important;
      cursor: pointer;
    }

    .nav-item:hover {
      background: rgba(255, 255, 255, 0.08) !important;
      color: #ffffff !important;
    }

    /* Active styling: white capsule with maroon text/icon and soft shadow */
    .nav-item.active {
      background: #ffffff !important;
      color: var(--layout-primary-maroon) !important;
      font-weight: 600 !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
      border-left: none !important;
    }

    .nav-item.active svg {
      color: var(--layout-primary-maroon) !important;
    }

    .nav-badge {
      margin-left: auto !important;
      background: #e11d48 !important;
      color: #ffffff !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      padding: 2px 8px !important;
      border-radius: 20px !important;
      line-height: 1 !important;
      box-shadow: 0 2px 6px rgba(225, 29, 72, 0.35) !important;
      transition: all 0.2s ease !important;
    }
    .nav-item.active .nav-badge {
      background: var(--layout-primary-maroon) !important;
      color: #ffffff !important;
      box-shadow: 0 2px 6px rgba(122, 15, 29, 0.3) !important;
    }

    .nav-icon {
      width: 20px !important;
      height: 20px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      flex-shrink: 0 !important;
    }

    .nav-icon svg {
      width: 17px;
      height: 17px;
    }

    /* Sidebar Footer */
    .sidebar-footer {
      background: transparent !important;
      border-top: 1px solid rgba(255, 255, 255, 0.06) !important;
      padding: 0 !important;
    }

    .logout-link {
      display: flex;
      align-items: center;
      gap: 12px;
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 500;
      transition: all 0.2s ease;
      cursor: pointer;
      padding: 16px 24px;
      border-left: none;
    }

    .logout-link:hover {
      color: #ffffff;
      background: rgba(255, 255, 255, 0.04);
    }

    .logout-link svg {
      width: 18px;
      height: 18px;
    }

    /* Main Wrapper */
    .main-wrapper {
      margin-left: 250px !important;
      width: calc(100% - 250px) !important;
      height: 100vh !important;
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden !important;
    }

    /* Content Area background */
    .content-area {
      background-color: var(--layout-light-bg) !important;
      padding: 32px 40px !important;
      flex: 1 !important;
      overflow-y: auto !important;
    }

    /* Topbar styling */
    .topbar {
      background: #ffffff !important;
      border-bottom: 1.5px solid #e2e8f0 !important;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01) !important;
      height: 70px !important;
      padding: 0 40px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
    }

    .topbar-title {
      font-family: 'Outfit', sans-serif !important;
      font-size: 20px !important;
      font-weight: 700 !important;
      color: var(--layout-primary-maroon) !important;
    }

    .topbar-right {
      display: flex !important;
      align-items: center !important;
      gap: 16px !important;
    }

    /* Custom Topbar Dropdowns & Widgets */
    .topbar-date-picker {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      padding: 8px 14px;
      font-size: 13px;
      color: var(--login-text-dark);
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
      transition: all 0.2s ease;
    }

    .topbar-date-picker:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
    }

    .topbar-date-picker svg {
      width: 16px;
      height: 16px;
      color: #64748b;
      flex-shrink: 0;
    }

    /* Bell Widget Container */
    .notif-dropdown-wrap {
      position: relative;
    }

    .topbar-bell-btn {
      width: 38px;
      height: 38px;
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #64748b;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
      transition: all 0.2s ease;
      position: relative;
    }

    .topbar-bell-btn:hover, .topbar-bell-btn.active {
      border-color: #cbd5e1;
      background: #f8fafc;
      color: var(--layout-primary-maroon);
    }

    .topbar-bell-btn svg {
      width: 18px;
      height: 18px;
    }

    .topbar-bell-btn .bell-badge {
      position: absolute;
      top: 10px;
      right: 11px;
      width: 6px;
      height: 6px;
      background: var(--layout-primary-maroon);
      border-radius: 50%;
    }

    /* Floating Notification Panel Overlay */
    .notif-panel {
      position: absolute;
      top: 100%;
      right: 0;
      width: 320px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.06), 0 24px 60px rgba(122, 15, 29, 0.03);
      margin-top: 10px;
      display: none;
      z-index: 150;
      overflow: hidden;
      animation: dropdownIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notif-panel.show {
      display: block;
    }

    @keyframes dropdownIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .notif-panel-header {
      padding: 14px 16px;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .notif-panel-title {
      font-family: 'Outfit', sans-serif;
      font-size: 14px;
      font-weight: 700;
      color: #1e293b;
    }

    .notif-panel-count {
      font-size: 11px;
      background: #fdf2f2;
      color: var(--layout-primary-maroon);
      padding: 2px 8px;
      border-radius: 20px;
      font-weight: 700;
    }

    .notif-list {
      display: flex;
      flex-direction: column;
      max-height: 280px;
      overflow-y: auto;
    }

    .notif-item {
      display: flex;
      gap: 12px;
      padding: 12px 16px;
      border-bottom: 1px solid #f8f9fa;
      transition: background 0.2s ease;
      text-decoration: none;
      color: inherit;
      text-align: left;
    }

    .notif-item:last-child {
      border-bottom: none;
    }

    .notif-item:hover {
      background: #f8fafc;
    }

    .notif-icon-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      color: var(--layout-primary-maroon);
      border: 1px solid #f1f5f9;
    }

    .notif-icon-circle svg {
      width: 14px;
      height: 14px;
    }

    .notif-item-body {
      display: flex;
      flex-direction: column;
      gap: 2px;
      min-width: 0;
    }

    .notif-item-text {
      font-size: 12px;
      font-weight: 600;
      color: #1e293b;
      line-height: 1.4;
    }

    .notif-item-desc {
      font-size: 11px;
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-top: 1px;
    }

    .notif-item-time {
      font-size: 10px;
      color: #94a3b8;
      margin-top: 3px;
    }

    .notif-empty {
      padding: 24px;
      text-align: center;
      font-size: 12.5px;
      color: #94a3b8;
    }

    /* Profile Widget with Solid Maroon Circular Avatar and White Initial Letter */
    .topbar-profile-dropdown {
      display: flex;
      align-items: center;
      gap: 12px;
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 30px;
      padding: 4px 16px 4px 4px;
      cursor: pointer;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
      transition: all 0.2s ease;
      position: relative;
    }

    .topbar-profile-dropdown:hover, .topbar-profile-dropdown.active {
      border-color: #cbd5e1;
      background: #f8fafc;
    }

    .topbar-profile-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--layout-primary-maroon) !important;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff !important;
      font-family: 'Outfit', sans-serif !important;
      font-weight: 800 !important;
      font-size: 13px !important;
      border: 1px solid rgba(255, 255, 255, 0.1);
      overflow: hidden;
    }

    .topbar-profile-text {
      display: flex;
      flex-direction: column;
      text-align: left;
    }

    .topbar-profile-name {
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      line-height: 1.2;
    }

    .topbar-profile-role {
      font-size: 11px;
      color: #64748b;
      margin-top: 1px;
    }

    .topbar-profile-chevron {
      width: 12px;
      height: 12px;
      color: #64748b;
    }

    /* Profile Menu Modal */
    .profile-menu {
      border-radius: 12px !important;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
      border: 1px solid #e2e8f0 !important;
    }

    /* Modal dialog restyling */
    .modal {
      border-radius: 20px !important;
      border: 1px solid rgba(0, 0, 0, 0.05) !important;
      box-shadow: 0 20px 50px rgba(122, 15, 29, 0.05) !important;
    }

    /* Responsive */
    @media (max-width: 991px) {
      .sidebar {
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
      }
      .sidebar.open {
        transform: translateX(0) !important;
      }
      .main-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
      }
      .topbar {
        padding: 0 20px !important;
      }
      .content-area {
        padding: 24px 20px !important;
      }
    }
  </style>
</head>
<body>

  {{-- ── SIDEBAR ── --}}
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="logo-wrap">
        <img src="{{ asset('images/logo_pt_bkm.png') }}" alt="Logo PT BKM" class="sidebar-logo-img">
        <div>
          <div class="logo-title">PT BKM</div>
          <div class="logo-sub">Sistem Keuangan</div>
        </div>
      </div>
      <button class="sidebar-close" id="sidebarClose" aria-label="Tutup">✕</button>
    </div>

    <nav class="sidebar-nav" id="sidebarNav">
      <div class="nav-section-label">Menu Utama</div>

      <!-- Dashboard Link -->
      <a data-href="{{ route('dashboard') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
          </svg>
        </span>
        Dashboard
      </a>

      <!-- Kas BKM -->
      <a data-href="{{ route('kas_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('kas_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </span>
        <span>Kas BKM</span>
        @if(($unreadCounts['kas_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['kas_bkm'] }}</span>
        @endif
      </a>

      <!-- Kas RAAB -->
      <a data-href="{{ route('kas_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('kas_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
        </span>
        <span>Kas RAAB</span>
        @if(($unreadCounts['kas_raab'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['kas_raab'] }}</span>
        @endif
      </a>

      <!-- Sisa Anggaran BKM -->
      <a data-href="{{ route('sisa_anggaran.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('sisa_anggaran.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </span>
        <span>Sisa Anggaran BKM</span>
        @if(($unreadCounts['sisa_anggaran'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['sisa_anggaran'] }}</span>
        @endif
      </a>

      <!-- Pengajuan Dana BKM -->
      <a data-href="{{ route('pengajuan_dana_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('pengajuan_dana_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
          </svg>
        </span>
        <span>Pengajuan Dana BKM</span>
        @if(($unreadCounts['pengajuan_dana_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['pengajuan_dana_bkm'] }}</span>
        @endif
      </a>

      <!-- Form Ajukan Dana BKM (Approval & Bukti) -->
      <a data-href="{{ route('form_ajukan_dana_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('form_ajukan_dana_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
            <path d="M9 14l2 2 4-4"/>
          </svg>
        </span>
        <span>Form Ajukan Dana BKM</span>
      </a>

      <!-- Pengajuan Dana RAAB -->
      <a data-href="{{ route('pengajuan_dana_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('pengajuan_dana_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
        </span>
        <span>Pengajuan Dana RAAB</span>
        @if(($unreadCounts['pengajuan_dana_raab'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['pengajuan_dana_raab'] }}</span>
        @endif
      </a>

      <!-- Form Ajukan Dana RAAB (Approval & Bukti) -->
      <a data-href="{{ route('form_ajukan_dana_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('form_ajukan_dana_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="2"/>
            <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
          </svg>
        </span>
        <span>Form Ajukan Dana RAAB</span>
      </a>

      <!-- Pengajuan Dana Diluar Operasional -->
      <a data-href="{{ route('pengajuan_dana_diluar_operasional.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('pengajuan_dana_diluar_operasional.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
          </svg>
        </span>
        <span>Pengajuan Dana Diluar Operasional</span>
        @if(($unreadCounts['pengajuan_dana_diluar_operasional'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['pengajuan_dana_diluar_operasional'] }}</span>
        @endif
      </a>

      <!-- Form Ajukan Dana Diluar Operasional (Approval & Bukti) -->
      <a data-href="{{ route('form_ajukan_dana_diluar_operasional.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('form_ajukan_dana_diluar_operasional.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <line x1="9" y1="15" x2="15" y2="15"/>
          </svg>
        </span>
        <span>Form Ajukan Dana Diluar Operasional</span>
      </a>

      <!-- Pengajuan Dana Gaji -->
      <a data-href="{{ route('pengajuan_dana_gaji.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('pengajuan_dana_gaji.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
          </svg>
        </span>
        <span>Pengajuan Dana Gaji</span>
      </a>

      <!-- Form Ajukan Dana Gaji (Approval & Bukti) -->
      <a data-href="{{ route('form_ajukan_dana_gaji.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('form_ajukan_dana_gaji.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <line x1="9" y1="15" x2="15" y2="15"/>
          </svg>
        </span>
        <span>Form Ajukan Dana Gaji</span>
      </a>

      <!-- Pengajuan Dana SKM -->
      <a data-href="{{ route('pengajuan_dana_skm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('pengajuan_dana_skm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
          </svg>
        </span>
        <span>Pengajuan Dana SKM</span>
      </a>

      <!-- Form Ajukan Dana SKM (Approval & Bukti) -->
      <a data-href="{{ route('form_ajukan_dana_skm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('form_ajukan_dana_skm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <line x1="9" y1="15" x2="15" y2="15"/>
          </svg>
        </span>
        <span>Form Ajukan Dana SKM</span>
      </a>

      <div class="nav-section-label" style="margin-top:20px">Master Data</div>

      <!-- Rekening BKM -->
      <a data-href="{{ route('master.rekening_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.rekening_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="21" x2="21" y2="21"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7 12 2"/>
          </svg>
        </span>
        <span>Rekening BKM</span>
        @if(($unreadCounts['rekening_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['rekening_bkm'] }}</span>
        @endif
      </a>

      <!-- Rekening RAAB -->
      <a data-href="{{ route('master.rekening_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.rekening_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
          </svg>
        </span>
        <span>Rekening RAAB</span>
        @if(($unreadCounts['rekening_raab'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['rekening_raab'] }}</span>
        @endif
      </a>

      <!-- Subrekening BKM -->
      <a data-href="{{ route('master.subrekening_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.subrekening_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
          </svg>
        </span>
        <span>Subrekening BKM</span>
        @if(($unreadCounts['subrekening_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['subrekening_bkm'] }}</span>
        @endif
      </a>

      <!-- Subrekening RAAB -->
      <a data-href="{{ route('master.subrekening_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.subrekening_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </span>
        <span>Subrekening RAAB</span>
        @if(($unreadCounts['subrekening_raab'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['subrekening_raab'] }}</span>
        @endif
      </a>

      <!-- Item BKM -->
      <a data-href="{{ route('master.item_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.item_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
          </svg>
        </span>
        <span>Item BKM</span>
        @if(($unreadCounts['item_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['item_bkm'] }}</span>
        @endif
      </a>

      <!-- Penyaluran Pembayaran BKM -->
      <a data-href="{{ route('master.penyaluran_pembayaran_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.penyaluran_pembayaran_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
          </svg>
        </span>
        <span>Penyaluran Pembayaran BKM</span>
        @if(($unreadCounts['penyaluran_bkm'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['penyaluran_bkm'] }}</span>
        @endif
      </a>

      <!-- Penyaluran Pembayaran RAAB -->
      <a data-href="{{ route('master.penyaluran_pembayaran_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('master.penyaluran_pembayaran_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
          </svg>
        </span>
        <span>Penyaluran Pembayaran RAAB</span>
        @if(($unreadCounts['penyaluran_raab'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['penyaluran_raab'] }}</span>
        @endif
      </a>

      <div class="nav-section-label" style="margin-top:20px">Integrasi Logistik</div>

      <!-- Pembelian Barang -->
      <a data-href="{{ route('logistik.pembelian_barang') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('logistik.pembelian_barang*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </span>
        <span>Pembelian Barang</span>
        @if(($unreadCounts['pembelian_barang'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['pembelian_barang'] }}</span>
        @endif
      </a>

      <!-- Kebutuhan Langsung -->
      <a data-href="{{ route('logistik.kebutuhan_langsung') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('logistik.kebutuhan_langsung*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </span>
        <span>Kebutuhan Langsung</span>
        @if(($unreadCounts['kebutuhan_langsung'] ?? 0) > 0)
          <span class="nav-badge">{{ $unreadCounts['kebutuhan_langsung'] }}</span>
        @endif
      </a>

      <div class="nav-section-label" style="margin-top:20px">Laporan</div>

      <!-- Laporan BKM -->
      <a data-href="{{ route('laporan.bkm') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('laporan.bkm') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </span>
        Laporan BKM
      </a>

      <!-- Laporan RAAB -->
      <a data-href="{{ route('laporan.raab') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('laporan.raab') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
        </span>
        Laporan RAAB
      </a>

      <!-- Laporan Tahunan -->
      <a data-href="{{ route('laporan.tahunan') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('laporan.tahunan') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </span>
        Laporan Tahunan
      </a>

      <div class="nav-section-label" style="margin-top:20px">Automasi</div>

      <!-- Automasi Kode Rekening RAAB -->
      <a data-href="{{ route('automasi.kode_rekening_raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('automasi.kode_rekening_raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
          </svg>
        </span>
        Automasi Kd Rek RAAB
      </a>

      <!-- Automasi RAAB -->
      <a data-href="{{ route('automasi.raab.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('automasi.raab.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
        </span>
        Automasi RAAB
      </a>

      <!-- Automasi Kode Rekening BKM -->
      <a data-href="{{ route('automasi.kode_rekening_bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('automasi.kode_rekening_bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/>
          </svg>
        </span>
        Automasi Kd Rek BKM
      </a>

      <!-- Automasi BKM -->
      <a data-href="{{ route('automasi.bkm.index') }}" onclick="window.location.href=this.dataset.href"
         class="nav-item {{ request()->routeIs('automasi.bkm.*') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </span>
        Automasi BKM
      </a>

      <!-- Pengaturan & Keamanan -->
      @auth
        @if(auth()->user()->canManageUsersAndSecurity())
          <div class="nav-section-label" style="margin-top:20px">Pengaturan & Keamanan</div>
          
          <a data-href="{{ route('users.index') }}" onclick="window.location.href=this.dataset.href"
             class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <span class="nav-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
              </svg>
            </span>
            Kelola Pengguna
          </a>

          <a data-href="{{ route('device_lockouts.index') }}" onclick="window.location.href=this.dataset.href"
             class="nav-item {{ request()->routeIs('device_lockouts.*', 'buka_blokir.*') ? 'active' : '' }}">
            <span class="nav-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
            Buka Blokir Device
          </a>

          <a data-href="{{ route('logs.index') }}" onclick="window.location.href=this.dataset.href"
             class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
            <span class="nav-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/>
              </svg>
            </span>
            Log Aktivitas
          </a>
        @elseif(auth()->user()->role === 'Admin Keuangan')
          <div class="nav-section-label" style="margin-top:20px">Pengaturan</div>
          
          <a data-href="{{ route('logs.index') }}" onclick="window.location.href=this.dataset.href"
             class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
            <span class="nav-icon">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/>
              </svg>
            </span>
            Log Aktivitas
          </a>
        @endif
      @endauth


    <!-- Logout footer link -->
    <div class="sidebar-footer">
      <a class="logout-link" onclick="openLogoutModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        <span>Keluar</span>
      </a>
    </div>
  </aside>

  {{-- Overlay mobile --}}
  <div class="overlay" id="overlay"></div>

  {{-- ── MAIN WRAPPER ── --}}
  <div class="main-wrapper">

    {{-- Topbar --}}
    <header class="topbar">
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
      <div class="topbar-title-section" style="display: flex; align-items: center; gap: 12px; text-align: left;">
        @hasSection('topbar-icon')
          <div style="color: var(--layout-primary-maroon); display: flex; align-items: center; background: rgba(122,15,29,0.06); width: 36px; height: 36px; border-radius: 8px; justify-content: center;">
            @yield('topbar-icon')
          </div>
        @endif
        <div style="display: flex; flex-direction: column;">
          <h1 class="topbar-title" style="margin: 0; line-height: 1.2; font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700; color: var(--layout-primary-maroon);">@yield('topbar-title', 'Dashboard')</h1>
          @hasSection('topbar-subtitle')
            <span class="topbar-subtitle" style="font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 1px;">@yield('topbar-subtitle')</span>
          @endif
        </div>
      </div>
      <div class="topbar-right">
        
        <!-- Dropdown Month selector showing current selected month -->
        <div class="topbar-date-picker">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          <span style="text-transform: capitalize;">{{ isset($activeDate) ? $activeDate->translatedFormat('F Y') : now()->translatedFormat('F Y') }}</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px; height:12px; margin-left:4px;">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </div>
        
        {{-- Hidden Theme Toggle (kept in code to prevent scripts error) --}}
        <div class="theme-toggle-wrap" style="display: none;">
          <input type="checkbox" id="themeCheckbox" />
        </div>

        {{-- Profile Dropdown --}}
        @auth
        <div class="profile-dropdown-wrap">
          <button class="topbar-profile-dropdown" id="profileBtn" aria-label="Menu Profil">
            <div class="topbar-profile-avatar">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="topbar-profile-text">
              <span class="topbar-profile-name">{{ auth()->user()->name }}</span>
              <span class="topbar-profile-role">{{ auth()->user()->role ?? 'Super Admin' }}</span>
            </div>
            <svg class="topbar-profile-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          
          <div class="profile-menu" id="profileMenu">
            <div class="profile-menu-header">
              <strong>{{ auth()->user()->name }}</strong>
              <span>{{ auth()->user()->email }}</span>
            </div>
            <div class="profile-menu-divider"></div>
            <button type="button" class="profile-menu-item logout-btn" onclick="openLogoutModal()" style="width:100%; border:none; background:none; text-align:left; cursor:pointer; font-family:inherit">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout
            </button>
          </div>
        </div>
        @endauth
      </div>
    </header>

    {{-- Page Content --}}
    <main class="content-area">
      {{-- Global Flash Messages --}}
      @if(session('success'))
      <div style="background: #e6f6ec; border: 1px solid #bbf7d0; color: #16a34a; padding: 14px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.08);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="background: #16a34a; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#16a34a; font-weight:700; cursor:pointer; font-size:16px; opacity:0.7;" title="Tutup">✕</button>
      </div>
      @endif

      @if(session('error'))
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 14px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="background: #dc2626; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </div>
          <span>{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#dc2626; font-weight:700; cursor:pointer; font-size:16px; opacity:0.7;" title="Tutup">✕</button>
      </div>
      @endif

      @if(session('info'))
      <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 14px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="background: #2563eb; color: #ffffff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </div>
          <span>{{ session('info') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:#2563eb; font-weight:700; cursor:pointer; font-size:16px; opacity:0.7;" title="Tutup">✕</button>
      </div>
      @endif

      @yield('content')
    </main>
  </div>

  {{-- Delete Confirm Modal ── --}}
  <div class="modal-backdrop" id="deleteModal" style="display:none">
    <div class="modal" style="max-width: 440px; padding: 30px 26px;">
      <div class="modal-icon" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; display: flex; align-items: center; justify-content: center; width: 54px; height: 54px; border-radius: 50%; margin: 0 auto 16px auto;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
        </svg>
      </div>
      <h3 class="modal-title" style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Konfirmasi Hapus</h3>
      <p class="modal-text" style="font-size: 13.5px; color: #475569; line-height: 1.5; margin-bottom: 24px; word-break: break-word;">Data yang dihapus tidak dapat dikembalikan. Yakin ingin melanjutkan?</p>
      <div class="modal-actions" style="display: flex; gap: 10px; justify-content: center;">
        <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()" style="min-width: 100px;">Batal</button>
        <form id="deleteForm" method="POST" style="margin:0;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger" style="min-width: 110px;">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Logout Confirm Modal ── --}}
  <div class="modal-backdrop" id="logoutModal" style="display:none">
    <div class="modal" id="logoutModalBox">
      <div class="modal-icon" style="background: rgba(122, 15, 29, 0.1); color: #7a0f1d; display: flex; align-items: center; justify-content: center; width: 54px; height: 54px; border-radius: 50%; margin: 0 auto 16px auto;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
      </div>
      <h3 class="modal-title">Konfirmasi Keluar</h3>
      <p class="modal-text">Apakah Anda yakin ingin logout, <strong>{{ auth()->check() ? auth()->user()->name : '' }}</strong>?</p>
      <div class="modal-actions">
        <button class="btn btn-ghost" onclick="closeLogoutModal()">Batal</button>
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="margin:0">
          @csrf
          <button type="submit" class="btn btn-danger">Ya, Logout</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Toast Greeting ── --}}
  @if(session('greeting'))
  <div class="toast-greeting" id="toastGreeting">
    <span class="toast-greeting-wave" style="display:inline-flex; align-items:center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </span>
    <span>{{ session('greeting') }}</span>
  </div>
  @endif

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    // Theme toggle dummy listener (prevents toggle check errors)
    const themeCheckbox = document.getElementById('themeCheckbox');
    if (themeCheckbox) {
      themeCheckbox.checked = true;
    }

    // Sidebar toggle (drawer on mobile viewports)
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');
    const hamburger = document.getElementById('hamburger');
    const closeBtn  = document.getElementById('sidebarClose');

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

    if (hamburger) hamburger.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Sidebar scroll position persistence & auto-scroll active item into view
    const navEl = document.getElementById('sidebarNav');
    if (navEl) {
      const restoreScroll = function() {
        const savedPos = sessionStorage.getItem('sidebarScrollPos');
        if (savedPos !== null) {
          navEl.scrollTop = parseInt(savedPos, 10);
        }
        
        const activeItem = navEl.querySelector('.nav-item.active');
        if (activeItem) {
          const navRect = navEl.getBoundingClientRect();
          const itemRect = activeItem.getBoundingClientRect();
          if (itemRect.top < navRect.top || itemRect.bottom > navRect.bottom || savedPos === null) {
            activeItem.scrollIntoView({ block: 'center', behavior: 'instant' });
          }
        }
      };

      restoreScroll();
      window.addEventListener('load', restoreScroll);

      navEl.addEventListener('scroll', function() {
        sessionStorage.setItem('sidebarScrollPos', navEl.scrollTop);
      }, { passive: true });

      navEl.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
          sessionStorage.setItem('sidebarScrollPos', navEl.scrollTop);
        });
      });
    }

    // Profile dropdown toggle
    const profileBtn  = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');
    
    if (profileBtn && profileMenu) {
      profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
        profileBtn.classList.toggle('active');
      });
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // Delete modal
    function confirmDelete(target, title = null, text = null) {
      let url = target;
      if (typeof target === 'object' && target !== null && target.dataset) {
        const btn = target;
        url = btn.dataset.url || url;
        title = btn.dataset.title || title;
        const itemName = btn.dataset.name || '';
        const itemType = btn.dataset.type || 'data';
        
        if (!title) {
          title = `Hapus ${itemType.charAt(0).toUpperCase() + itemType.slice(1)}?`;
        }
        
        if (!text) {
          if (itemName) {
            text = `Apakah Anda yakin ingin menghapus ${itemType} <strong style="color: #7a0f1d; word-break: break-word;">&ldquo;${escapeHtml(itemName)}&rdquo;</strong>?<br><span style="font-size: 12px; color: #64748b; margin-top: 8px; display: inline-block;">Tindakan ini permanen dan data yang dihapus tidak dapat dikembalikan.</span>`;
          } else {
            text = `Apakah Anda yakin ingin menghapus ${itemType} ini?<br><span style="font-size: 12px; color: #64748b; margin-top: 8px; display: inline-block;">Tindakan ini permanen dan data yang dihapus tidak dapat dikembalikan.</span>`;
          }
        }
      }

      document.getElementById('deleteForm').action = url;
      const titleEl = document.querySelector('#deleteModal .modal-title');
      const textEl = document.querySelector('#deleteModal .modal-text');
      if (titleEl) titleEl.innerText = title || 'Konfirmasi Hapus';
      if (textEl) {
        textEl.innerHTML = text || 'Data yang dihapus tidak dapat dikembalikan. Yakin ingin melanjutkan?';
      }
      const m = document.getElementById('deleteModal');
      m.style.display = 'flex';
    }
    function closeDeleteModal() {
      const m = document.getElementById('deleteModal');
      m.style.display = 'none';
    }
    const deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
      deleteModalEl.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
      });
    }

    // Logout modal
    function openLogoutModal() {
      const m = document.getElementById('logoutModal');
      m.style.display = 'flex';
      requestAnimationFrame(() => {
        m.classList.add('modal-show');
      });
    }
    function closeLogoutModal() {
      const m = document.getElementById('logoutModal');
      m.classList.remove('modal-show');
      m.classList.add('modal-hide');
      setTimeout(() => {
        m.style.display = 'none';
        m.classList.remove('modal-hide');
      }, 280);
    }
    const logoutModal = document.getElementById('logoutModal');
    if (logoutModal) {
      logoutModal.addEventListener('click', function(e) {
        if (e.target === this) closeLogoutModal();
      });
    }

    // Toast greeting
    const toast = document.getElementById('toastGreeting');
    if (toast) {
      setTimeout(() => toast.classList.add('toast-in'), 100);
      setTimeout(() => {
        toast.classList.remove('toast-in');
        toast.classList.add('toast-out');
        setTimeout(() => toast.remove(), 600);
      }, 4500);
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Global: Format Rupiah Input (pemisah ribuan dengan titik) --}}
  <script>
    // Format angka dengan pemisah ribuan (titik) saat user mengetik
    function formatRupiahInput(input) {
      let cursorPos = input.selectionStart;
      let oldLen = input.value.length;
      let value = input.value.replace(/[^0-9]/g, '');
      if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(value);
      } else {
        input.value = '';
      }
      // Adjust cursor position after formatting
      let newLen = input.value.length;
      let diff = newLen - oldLen;
      input.setSelectionRange(cursorPos + diff, cursorPos + diff);
    }

    // Saat form di-submit, hapus titik pemisah ribuan agar server menerima angka murni
    document.addEventListener('submit', function(e) {
      const form = e.target;
      const rupiahInputs = form.querySelectorAll('.rupiah-input');
      rupiahInputs.forEach(function(input) {
        input.value = input.value.replace(/\./g, '');
      });
    }, true);
  </script>

  @stack('scripts')
</body>
</html>

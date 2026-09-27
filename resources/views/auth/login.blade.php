<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login – Sistem Keuangan PT BKM</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logo_pt_bkm.png') }}" />
  <link rel="shortcut icon" href="{{ asset('images/logo_pt_bkm.png') }}" />
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    // Force light mode on login page to match the clean white and maroon layout
    document.documentElement.classList.add('theme-light');
    document.documentElement.classList.remove('theme-dark');
  </script>
  <style>
    :root {
      --login-primary-maroon: #7a0f1d;
      --login-dark-maroon: #3d030a;
      --login-light-bg: #f8f9fa;
      --login-gold: #dfbb6d;
      --login-gold-hover: #c59b27;
      --login-text-dark: #1e293b;
      --login-text-muted: #64748b;
      --login-border-color: #e2e8f0;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--login-light-bg);
      color: var(--login-text-dark);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }

    .login-wrapper {
      display: flex;
      width: 100vw;
      height: 100vh;
      position: relative;
      overflow: hidden;
    }

    /* Sidebar Styling (Left Column) */
    .login-sidebar {
      position: relative;
      width: 42vw;
      height: 100vh;
      clip-path: url(#login-sidebar-clip-path);
      -webkit-clip-path: url(#login-sidebar-clip-path);
      background: linear-gradient(135deg, var(--login-primary-maroon) 0%, var(--login-dark-maroon) 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 60px 80px 60px 60px;
      box-sizing: border-box;
      z-index: 5;
      overflow: hidden;
    }

    .login-sidebar-bg-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('{{ asset("images/login_sidebar_bg.jpg") }}');
      background-size: cover;
      background-position: center;
      mix-blend-mode: overlay;
      opacity: 0.85;
      z-index: 1;
      pointer-events: none;
    }

    .login-sidebar-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(122, 15, 29, 0.45) 0%, rgba(61, 3, 10, 0.85) 100%);
      z-index: 2;
      pointer-events: none;
    }

    .login-sidebar-content {
      position: relative;
      z-index: 3;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      max-width: 32vw; /* Ensure content stays safely inside unclipped zone */
    }

    /* Brand Header with explicit resets to prevent conflicts with app.css global classes */
    .login-sidebar-header {
      display: flex !important;
      align-items: center !important;
      gap: 16px !important;
      background: transparent !important;
      border: none !important;
      padding: 0 !important;
      margin: 0 !important;
      box-shadow: none !important;
      justify-content: flex-start !important;
    }

    .login-sidebar-logo {
      width: 48px !important;
      height: 48px !important;
      background: #ffffff !important;
      border: 2px solid var(--login-gold) !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
      padding: 3px !important;
      overflow: hidden !important;
      flex-shrink: 0 !important;
    }

    .login-sidebar-logo img {
      width: 100% !important;
      height: 100% !important;
      object-fit: contain !important;
      border-radius: 50% !important;
    }

    .login-sidebar-header-text {
      display: flex !important;
      flex-direction: column !important;
      align-items: flex-start !important;
      text-align: left !important;
    }

    .login-sidebar-title {
      color: #ffffff !important;
      font-family: 'Outfit', sans-serif !important;
      font-size: 20px !important;
      font-weight: 700 !important;
      line-height: 1.2 !important;
    }

    .login-sidebar-subtitle {
      color: rgba(255, 255, 255, 0.7) !important;
      font-size: 12px !important;
      font-weight: 400 !important;
      margin-top: 2px !important;
    }

    /* Sidebar Main Hero Text */
    .login-sidebar-middle {
      margin-top: auto;
      margin-bottom: auto;
      text-align: left;
    }

    .login-sidebar-divider {
      width: 40px;
      height: 3px;
      background-color: var(--login-gold);
      border-radius: 2px;
      margin-bottom: 24px;
    }

    .login-sidebar-hero-text {
      font-family: 'Outfit', sans-serif;
      font-size: 38px;
      font-weight: 700;
      line-height: 1.25;
      color: #ffffff;
      letter-spacing: -0.5px;
    }

    .login-sidebar-hero-text .highlight {
      color: var(--login-gold);
    }

    .login-sidebar-desc {
      font-size: 14.5px;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.6;
      margin-top: 20px;
    }

    /* Sidebar Footer */
    .login-sidebar-footer {
      font-size: 12.5px;
      color: rgba(255, 255, 255, 0.5);
      text-align: left;
    }

    /* Glowing Gold Curve (Splitting Path) */
    .login-sidebar-glow-divider {
      position: absolute;
      top: 0;
      left: 0;
      width: 42vw;
      height: 100vh;
      z-index: 10;
      pointer-events: none;
    }

    /* Main Area (Right Column) */
    .login-main {
      position: relative;
      flex: 1;
      height: 100vh;
      background-color: #fdfdfd;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1;
    }

    /* Decorative Background Elements */
    .login-pattern-circles {
      position: absolute;
      top: -50px;
      right: -50px;
      width: 320px;
      height: 320px;
      opacity: 0.75;
      pointer-events: none;
    }

    .login-pattern-dots {
      position: absolute;
      bottom: 40px;
      right: 40px;
      width: 80px;
      height: 120px;
      opacity: 0.85;
      pointer-events: none;
    }

    /* Login Card Styling */
    .login-card-container {
      width: 100%;
      max-width: 450px;
      background: #ffffff;
      border-radius: 24px;
      padding: 48px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02), 0 24px 70px rgba(122, 15, 29, 0.05);
      text-align: center;
      z-index: 10;
      position: relative;
      border: 1px solid rgba(0, 0, 0, 0.01);
    }

    .login-card-icon-circle {
      width: 80px;
      height: 80px;
      background: var(--login-primary-maroon);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 24px rgba(122, 15, 29, 0.2);
    }

    .login-card-icon-circle svg {
      width: 34px;
      height: 34px;
    }

    .login-card-heading {
      font-family: 'Outfit', sans-serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--login-text-dark);
      letter-spacing: -0.5px;
    }

    .login-highlight-maroon {
      color: var(--login-primary-maroon);
    }

    .login-card-divider {
      width: 35px;
      height: 2px;
      background-color: var(--login-gold);
      margin: 16px auto;
      border-radius: 1px;
    }

    .login-card-subtext {
      font-size: 14.5px;
      color: var(--login-text-muted);
      line-height: 1.5;
      margin-bottom: 32px;
    }

    .login-font-semibold {
      font-weight: 600;
    }

    /* Button Google */
    .login-btn-google {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      width: 100%;
      padding: 14px;
      background: #ffffff;
      color: var(--login-text-dark);
      border: 1px solid var(--login-border-color);
      border-radius: 10px;
      font-size: 14.5px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .login-btn-google:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transform: translateY(-1px);
    }

    .login-btn-google svg {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
    }

    /* Alerts custom styling inside card */
    .login-alert-danger {
      background: rgba(239, 68, 68, 0.06);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #b91c1c;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 24px;
      text-align: left;
      line-height: 1.5;
      display: flex;
      gap: 10px;
      align-items: flex-start;
    }

    .login-alert-success {
      background: rgba(16, 185, 129, 0.06);
      border: 1px solid rgba(16, 185, 129, 0.2);
      color: #047857;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 24px;
      text-align: left;
      line-height: 1.5;
      display: flex;
      gap: 10px;
      align-items: flex-start;
    }

    /* Security Notice */
    .login-card-security-note {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      gap: 8px;
      margin-top: 32px;
      font-size: 12px;
      color: var(--login-text-muted);
      line-height: 1.5;
      text-align: left;
      max-width: 320px;
      margin-left: auto;
      margin-right: auto;
    }

    .login-card-security-note svg {
      width: 16px;
      height: 16px;
      stroke: var(--login-primary-maroon);
      flex-shrink: 0;
      margin-top: 2px;
    }

    /* Responsive design */
    @media (max-width: 991px) {
      .login-sidebar {
        display: none;
      }
      .login-sidebar-glow-divider {
        display: none;
      }
      .login-main {
        width: 100%;
        background-color: var(--login-light-bg);
      }
      .login-card-container {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        padding: 36px 24px;
        max-width: 400px;
        margin: 20px;
      }
      .login-pattern-circles, .login-pattern-dots {
        display: none;
      }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
    <!-- Left Column: Premium Sidebar -->
    <div class="login-sidebar">
      <div class="login-sidebar-bg-image"></div>
      <div class="login-sidebar-overlay"></div>
      <div class="login-sidebar-content">
        <!-- Brand Header -->
        <div class="login-sidebar-header">
          <div class="login-sidebar-logo">
            <img src="{{ asset('images/logo_pt_bkm.png') }}" alt="Logo PT BKM">
          </div>
          <div class="login-sidebar-header-text">
            <span class="login-sidebar-title">PT BKM</span>
            <span class="login-sidebar-subtitle">Sistem Manajemen Keuangan Modern</span>
          </div>
        </div>

        <!-- Middle Pitch -->
        <div class="login-sidebar-middle">
          <div class="login-sidebar-divider"></div>
          <h2 class="login-sidebar-hero-text">
            Kelola Keuangan,<br>Bangun <span class="highlight">Kepercayaan.</span>
          </h2>
          <p class="login-sidebar-desc">
            Sistem keuangan modern yang dirancang untuk mendukung efisiensi, akurasi, dan transparansi dalam setiap proses bisnis.
          </p>
        </div>

        <!-- Footer Info -->
        <div class="login-sidebar-footer">
          &copy; 2026 PT BKM. All rights reserved.
        </div>
      </div>
    </div>

    <!-- The Glowing Curved Divider Shape -->
    <svg class="login-sidebar-glow-divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="login-gold-grad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#dfbb6d" />
          <stop offset="50%" stop-color="#c59b27" />
          <stop offset="100%" stop-color="#8e6a14" />
        </linearGradient>
      </defs>
      <!-- Main golden curve -->
      <path d="M 85,0 C 97,30 97,70 90,100" fill="none" stroke="url(#login-gold-grad)" stroke-width="1.2" vector-effect="non-scaling-stroke" />
      <!-- Secondary echo curve (opacity 0.2) -->
      <path d="M 87,0 C 99,30 99,70 92,100" fill="none" stroke="url(#login-gold-grad)" stroke-width="0.6" opacity="0.35" vector-effect="non-scaling-stroke" />
    </svg>

    <!-- Right Column: Login Card Area -->
    <div class="login-main">
      <!-- Decorative circles top right -->
      <div class="login-pattern-circles">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="100" cy="100" r="95" stroke="#f1f5f9" stroke-width="2"/>
          <circle cx="100" cy="100" r="70" stroke="#f1f5f9" stroke-width="1.5" stroke-dasharray="4 4"/>
          <circle cx="100" cy="100" r="45" stroke="#e2e8f0" stroke-width="1"/>
        </svg>
      </div>

      <!-- Decorative dots bottom right -->
      <div class="login-pattern-dots">
        <svg width="80" height="120" viewBox="0 0 80 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="10" cy="10" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="10" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="10" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="10" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="10" cy="30" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="30" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="30" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="30" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="10" cy="50" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="50" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="50" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="50" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="10" cy="70" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="70" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="70" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="70" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="10" cy="90" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="90" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="90" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="90" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="10" cy="110" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="30" cy="110" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="50" cy="110" r="3" fill="#7a0f1d" opacity="0.08"/>
          <circle cx="70" cy="110" r="3" fill="#7a0f1d" opacity="0.08"/>
        </svg>
      </div>

      <!-- Core Login Card -->
      <div class="login-card-container">
        <!-- Top Icon -->
        <div class="login-card-icon-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
            <!-- Document outline -->
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z" stroke="#ffffff" stroke-linejoin="round"/>
            <path d="M14 2v6h6" stroke="#ffffff" stroke-linejoin="round"/>
            <!-- Line Graph -->
            <path d="M8 15l2.5-2.5 2 2 4.5-4.5" stroke="#dfbb6d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <!-- Rp label inside document -->
            <text x="6.5" y="10.5" fill="#ffffff" font-family="'Outfit', sans-serif" font-size="4" font-weight="bold">Rp</text>
          </svg>
        </div>

        <!-- Heading -->
        <h1 class="login-card-heading">Selamat <span class="login-highlight-maroon">Datang!</span></h1>
        
        <div class="login-card-divider"></div>
        
        <p class="login-card-subtext">Login untuk mengakses<br><span class="login-highlight-maroon login-font-semibold">Sistem Keuangan PT BKM</span></p>

        <!-- Dynamic Laravel Validation Alerts -->
        @if(session('error') || isset($blockedMessage))
          <div class="login-alert-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>{{ session('error') ?? $blockedMessage }}</div>
          </div>
        @endif

        @if(session('success'))
          <div class="login-alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:2px;">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <div>{{ session('success') }}</div>
          </div>
        @endif

        <!-- Google Authentication Button -->
        <a href="{{ route('auth.google') }}" class="login-btn-google">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
          </svg>
          Masuk dengan Akun Google
        </a>

        <!-- Secure Area Footer Notice -->
        <div class="login-card-security-note">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 11 11 13 15 9"/>
          </svg>
          <span>Akses aman hanya untuk pengguna keuangan yang telah terdaftar.</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Inline Vector ClipPath for Sidebar Curve -->
  <svg width="0" height="0" style="position: absolute;">
    <defs>
      <clipPath id="login-sidebar-clip-path" clipPathUnits="objectBoundingBox">
        <path d="M 0,0 L 0.85,0 C 0.97,0.3 0.97,0.7 0.90,1 L 0,1 Z" />
      </clipPath>
    </defs>
  </svg>

</body>
</html>

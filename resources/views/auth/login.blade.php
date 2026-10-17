<x-guest-layout>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            box-shadow: none !important;
        }

        :root {
            --color-primary:      #2e7d32;   /* Hutan Sulbar */
            --color-primary-dark: #1b5e20;   /* Hutan Gelap */
            --color-surface:      #e8f5e9;   /* Embun */
            --color-bg:           #f7faf7;   /* Kertas */
            --color-ink:          #0f172a;   /* Tinta Utama Slate-900 */
            --color-ink-muted:    #334155;   /* Tinta Sekunder Slate-700 (Kontras Tinggi) */
            --font-heading:       'Bricolage Grotesque', system-ui, -apple-system, sans-serif;
            --font-body:          'Public Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-ink);
            font-family: var(--font-body);
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .login-page-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            min-height: 100vh;
            background-color: var(--color-bg);
        }

        /* ─── Kolom Kiri: Formulir Kedinasan ─── */
        .login-pane-form {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 64px;
            background: #ffffff;
            border-right: 2px solid var(--color-surface);
        }

        .login-nav-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .login-back-link {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-primary);
            text-decoration: underline;
            text-underline-offset: 4px;
            transition: color 0.15s ease;
        }

        .login-back-link:hover {
            color: var(--color-primary-dark);
        }

        .login-form-inner {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }

        .brand-logo-only {
            display: block;
            height: 52px;
            width: auto;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .login-heading {
            font-family: var(--font-heading);
            font-size: clamp(42px, 5.2vw, 72px);
            font-weight: 800;
            color: var(--color-ink);
            letter-spacing: -0.04em;
            line-height: 0.96;
            margin-bottom: 16px;
        }

        .login-lead {
            font-size: 16px;
            color: var(--color-ink-muted);
            line-height: 1.55;
            margin-bottom: 28px;
            max-width: 54ch;
        }

        .status-alert {
            background-color: var(--color-surface);
            color: var(--color-primary-dark);
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 16px;
            font-weight: 700;
            color: var(--color-ink);
            margin-bottom: 8px;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .forgot-link {
            font-size: 16px;
            font-weight: 600;
            color: var(--color-primary);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .forgot-link:hover {
            color: var(--color-primary-dark);
        }

        .form-input-field {
            width: 100%;
            height: 50px;
            padding: 12px 16px;
            font-family: var(--font-body);
            font-size: 16px;
            color: var(--color-ink);
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s ease;
            box-shadow: none !important;
        }

        .form-input-field:focus {
            border-color: var(--color-primary);
        }

        .form-error {
            color: #b71c1c;
            font-size: 16px;
            font-weight: 600;
            margin-top: 6px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 500;
            color: var(--color-ink-muted);
            cursor: pointer;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--color-primary);
            cursor: pointer;
        }

        .btn-submit-login {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 52px;
            background: var(--color-primary);
            color: #ffffff;
            font-family: var(--font-body);
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.1s ease;
            box-shadow: none !important;
        }

        .btn-submit-login:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }

        .btn-submit-login:active {
            transform: translateY(0);
        }

        .security-footer {
            margin-top: 32px;
            font-size: 16px;
            color: var(--color-ink-muted);
            line-height: 1.55;
            border-top: 2px solid var(--color-surface);
            padding-top: 16px;
            max-width: 54ch;
        }

        /* ─── Kolom Kanan: Panel Pusat Kendali Komando & Protokol (Bukan Gambar) ─── */
        .login-pane-visual {
            background: linear-gradient(145deg, #f3f8f3 0%, #e8f5e9 100%);
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
            border-left: 1px solid #d0e7d2;
        }

        .command-panel-card {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .command-status-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid #bbf7d0;
            padding: 6px 14px;
            border-radius: 999px;
            align-self: flex-start;
        }

        .pulse-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(22, 163, 74, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
            }
        }

        .command-status-text {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-primary-dark);
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .visual-headline-block {
            margin-bottom: 0;
        }

        .visual-title {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 800;
            color: var(--color-ink);
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .visual-desc {
            font-size: 15px;
            color: var(--color-ink-muted);
            line-height: 1.55;
            max-width: 58ch;
        }

        .command-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .command-stat-item {
            background: #ffffff;
            border: 1.5px solid #d6ebd7;
            border-radius: 10px;
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .command-stat-item:hover {
            transform: translateY(-2px);
            border-color: var(--color-primary);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.07);
        }

        .command-stat-num {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1.1;
        }

        .command-stat-lbl {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-ink);
        }

        .command-stat-sub {
            font-size: 12px;
            color: var(--color-ink-muted);
            line-height: 1.4;
        }

        .command-protocol-box {
            background: #ffffff;
            border: 1.5px solid #d6ebd7;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .command-protocol-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 800;
            color: var(--color-primary-dark);
            padding-bottom: 8px;
            border-bottom: 1px dashed #d6ebd7;
        }

        .command-protocol-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .command-protocol-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--color-ink);
            line-height: 1.5;
        }

        .protocol-icon-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--color-primary);
            flex-shrink: 0;
            margin-top: 6px;
        }

        .command-security-notice {
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid #c8e6c9;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .security-notice-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--color-ink-muted);
            line-height: 1.4;
        }

        .security-notice-text svg {
            color: var(--color-primary);
            flex-shrink: 0;
        }

        /* ─── Responsif Mobile ─── */
        @media (max-width: 1024px) {
            .login-page-grid {
                grid-template-columns: 1fr;
            }
            .login-pane-form {
                border-right: none;
                padding: 40px 24px;
            }
            .login-pane-visual {
                padding: 40px 24px;
                border-top: 2px solid var(--color-surface);
                border-left: none;
            }
        }

        @media (max-width: 640px) {
            .login-heading {
                font-size: 28px;
                line-height: 1.1;
            }
            .command-stats-grid {
                grid-template-columns: 1fr;
            }
            .login-pane-form, .login-pane-visual {
                padding: 24px 16px;
            }
            .visual-title {
                font-size: 22px;
            }
        }

        /* ─── Theme Toggle Button ─── */
        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--color-surface);
            color: var(--color-primary-dark);
            border: 1px solid #81c784;
            font-family: var(--font-body);
            line-height: 1;
            white-space: nowrap;
        }

        .theme-toggle-btn:hover {
            background: #d4ebd4;
            color: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        /* ─── Dark Mode Overrides for Login Page ─── */
        html[data-theme="dark"] {
            --color-primary:      #4caf50;
            --color-primary-dark: #81c784;
            --color-surface:      #162335;
            --color-bg:           #0B0F1A;
            --color-ink:          #F0F4FF;
            --color-ink-muted:    #9BA8C2;
        }

        html[data-theme="dark"],
        html[data-theme="dark"] body {
            background-color: #0B0F1A !important;
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .login-page-grid {
            background-color: #0B0F1A !important;
        }

        html[data-theme="dark"] .login-pane-form {
            background: #0f1726 !important;
            border-right: 2px solid rgba(240, 244, 255, 0.08) !important;
        }

        html[data-theme="dark"] .login-pane-visual {
            background: linear-gradient(145deg, #090e18 0%, #0d1524 100%) !important;
        }

        html[data-theme="dark"] .login-back-link {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .login-heading {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .login-lead {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .theme-toggle-btn {
            background: #162438 !important;
            color: #81c784 !important;
            border-color: rgba(76, 175, 80, 0.4) !important;
        }

        html[data-theme="dark"] .theme-toggle-btn:hover {
            background: #1f3552 !important;
            color: #a5d6a7 !important;
            border-color: #4caf50 !important;
        }

        html[data-theme="dark"] .form-label {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .form-input-field {
            background-color: #162235 !important;
            border-color: rgba(240, 244, 255, 0.14) !important;
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .form-input-field:focus {
            border-color: #4caf50 !important;
            background-color: #1a283e !important;
        }

        html[data-theme="dark"] .remember-label {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .forgot-link {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .command-badge-wrap {
            background: #142232 !important;
            border-color: rgba(76, 175, 80, 0.3) !important;
        }

        html[data-theme="dark"] .command-status-text {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .visual-title {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .visual-desc {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .command-stat-item {
            background: #111a28 !important;
            border-color: rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .command-stat-item:hover {
            border-color: #4caf50 !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
        }

        html[data-theme="dark"] .command-stat-num {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .command-stat-lbl {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .command-stat-sub {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .command-protocol-box {
            background: #111a28 !important;
            border-color: rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .protocol-title {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .protocol-step {
            color: #CAD5E2 !important;
        }

        html[data-theme="dark"] .login-footer-meta {
            border-top-color: rgba(240, 244, 255, 0.08) !important;
            color: #6B7896 !important;
        }
    </style>

    <div class="login-page-grid">
        <!-- Kolom Kiri: Formulir Kedinasan -->
        <main class="login-pane-form">
            <nav class="login-nav-top animate-in" aria-label="Navigasi Masuk">
                <a href="/" class="login-back-link">
                    &larr; Kembali ke Beranda
                </a>
                <button id="login-theme-toggle" class="theme-toggle-btn" onclick="toggleTheme()" type="button" title="Ganti Tema (Gelap / Terang)" aria-label="Ganti Tema Tampilan">
                    <span id="theme-icon" style="display:inline-flex;align-items:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </span>
                    <span id="theme-label">Tema Gelap</span>
                </button>
            </nav>

            <div class="login-form-inner">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Diskoperindag Provinsi Sulawesi Barat" class="brand-logo-only animate-in">

                <h1 class="login-heading animate-in">Masuk Sigap Pasar</h1>
                
                <p class="login-lead animate-in">
                    Portal terpadu pengawasan siber media sosial, verifikasi faktual lapangan, dan operasi stabilisasi pasokan pangan enam kabupaten.
                </p>

                @if (session('status'))
                    <div class="status-alert animate-in">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <!-- Email Address -->
                    <div class="form-row animate-in">
                        <label for="email" class="form-label">Email Kedinasan</label>
                        <input id="email" 
                               class="form-input-field" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="Masukkan Email">
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-row animate-in">
                        <div class="label-row">
                            <label for="password" class="form-label">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                     Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <input id="password" 
                               class="form-input-field"
                               type="password"
                               name="password"
                               value=""
                               required 
                               autocomplete="current-password"
                               placeholder="Masukkan kata sandi">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-wrap animate-in">
                        <label for="remember_me" class="remember-label">
                            <input id="remember_me" type="checkbox" class="remember-checkbox" name="remember" checked>
                            <span>Ingat sesi masuk di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login animate-in">
                        Masuk ke Sistem &rarr;
                    </button>
                </form>
            </div>

            <footer class="security-footer animate-in">
                Akses terbatas hanya untuk aparatur Diskoperindag Prov. Sulbar dan Satgas Pangan Daerah. Setiap aktivitas masuk tercatat dalam jejak audit.
            </footer>
        </main>

        <!-- Kolom Kanan: Panel Pusat Kendali Komando & Protokol Ketahanan Pangan (Bukan Gambar) -->
        <aside class="login-pane-visual">
            <div class="command-panel-card animate-in">
                <!-- Status Header -->
                <div class="command-status-header">
                    <span class="pulse-indicator"></span>
                    <span class="command-status-text">Sistem Operasional Resmi &bull; Prov. Sulawesi Barat</span>
                </div>

                <!-- Headline -->
                <div class="visual-headline-block">
                    <h2 class="visual-title">Pusat Kendali Stabilisasi Pangan & Inflasi Daerah</h2>
                    <p class="visual-desc">
                        Portal operasional terpadu aparatur Diskoperindag Prov. Sulbar dan Satgas Pangan untuk pengawasan disparitas harga, integritas pasokan, dan mitigasi gejolak pasar rakyat di enam kabupaten.
                    </p>
                </div>

                <!-- 4 Quick Stats / Live Indicators -->
                <div class="command-stats-grid">
                    <div class="command-stat-item">
                        <div class="command-stat-num">6 Wilayah</div>
                        <div class="command-stat-lbl">Cakupan Kabupaten</div>
                        <div class="command-stat-sub">Mamuju, Majene, Polman, Mamasa, Pasangkayu, Mateng</div>
                    </div>
                    <div class="command-stat-item">
                        <div class="command-stat-num">5 Aktor</div>
                        <div class="command-stat-lbl">Rantai Komando</div>
                        <div class="command-stat-sub">Siber &bull; Verifikator &bull; Admin &bull; Petugas &bull; Pimpinan</div>
                    </div>
                    <div class="command-stat-item">
                        <div class="command-stat-num">&lt; 2 Jam</div>
                        <div class="command-stat-lbl">Target Verifikasi</div>
                        <div class="command-stat-sub">Standar respon penyaringan dan penerbitan surat tugas</div>
                    </div>
                    <div class="command-stat-item">
                        <div class="command-stat-num">GPS Riil</div>
                        <div class="command-stat-lbl">Validasi Geotagging</div>
                        <div class="command-stat-sub">Pengecekan fisik harga pedagang terikat koordinat pasar</div>
                    </div>
                </div>

                <!-- 3 Protokol Ketahanan Pangan -->
                <div class="command-protocol-box">
                    <div class="command-protocol-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Tiga Protokol Utama Pengawasan Pasar
                    </div>
                    <div class="command-protocol-list">
                        <div class="command-protocol-item">
                            <div class="protocol-icon-dot"></div>
                            <div>
                                <strong>Deteksi & Skoring Virality Siber:</strong> Memindai linimasa keluhan warga secara objektif untuk mencegah kepanikan dan spekulasi pedagang.
                            </div>
                        </div>
                        <div class="command-protocol-item">
                            <div class="protocol-icon-dot"></div>
                            <div>
                                <strong>Audit Faktual & Komparasi HET:</strong> Penugasan petugas ke pasar tradisional untuk mengecek timbangan dan harga acuan Bapanas.
                            </div>
                        </div>
                        <div class="command-protocol-item">
                            <div class="protocol-icon-dot"></div>
                            <div>
                                <strong>Intervensi & Dashboard Pimpinan:</strong> Eksekusi operasi pasar murah, penyaluran beras SPHP Bulog, dan ringkasan eksekutif pengambil kebijakan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security & Timezone Notice -->
                <div class="command-security-notice">
                    <div class="security-notice-text">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Hak Akses Terotentikasi &bull; Zona Waktu: <strong>WITA (UTC+8)</strong> &bull; Jejak Audit Enkripsi Aktif</span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Script Motion Entrance -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            if (!mediaQuery.matches) {
                const nodes = document.querySelectorAll('.animate-in');
                nodes.forEach((el, i) => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(16px)';
                    el.style.transition = 'opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 50 * i + 80);
                });
            }
        });

        // Logika Tema Terang / Gelap
        function applyThemeUI(theme) {
            const icon = document.getElementById('theme-icon');
            const label = document.getElementById('theme-label');
            const btn = document.getElementById('login-theme-toggle');
            
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                if (icon) {
                    icon.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
                }
                if (label) label.textContent = 'Tema Terang';
                if (btn) btn.setAttribute('title', 'Beralih ke Tema Terang');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                if (icon) {
                    icon.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
                }
                if (label) label.textContent = 'Tema Gelap';
                if (btn) btn.setAttribute('title', 'Beralih ke Tema Gelap');
            }
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            try {
                localStorage.setItem('sigap_theme', next);
            } catch (e) {}
            applyThemeUI(next);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const saved = localStorage.getItem('sigap_theme');
            applyThemeUI(saved === 'dark' ? 'dark' : 'light');
        });
    </script>
</x-guest-layout>

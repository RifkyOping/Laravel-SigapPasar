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
            --font-heading:       'Poppins', system-ui, -apple-system, sans-serif;
            --font-body:          'Poppins', system-ui, -apple-system, sans-serif;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-ink);
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .login-page-grid {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            background-color: var(--color-bg);
            padding: 20px 16px;
            box-sizing: border-box;
        }

        /* ─── Formulir Kedinasan ─── */
        .login-pane-form {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 490px;
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: 18px;
            padding: 36px 40px;
            box-sizing: border-box;
            box-shadow: 0 4px 24px rgba(46, 125, 50, 0.06);
        }

        .login-nav-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-shrink: 0;
        }

        .login-back-link {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-primary);
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 0.15s ease;
        }

        .login-back-link:hover {
            color: var(--color-primary-dark);
        }

        .login-form-inner {
            width: 100%;
        }

        .brand-logo-only {
            display: block;
            height: 48px;
            width: auto;
            object-fit: contain;
            margin-bottom: 14px;
        }

        .login-heading {
            font-family: var(--font-heading);
            font-size: 28px;
            font-weight: 800;
            color: var(--color-ink);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .status-alert {
            background-color: var(--color-surface);
            color: var(--color-primary-dark);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .login-error-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1.5px solid #fecaca;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 18px;
            line-height: 1.45;
        }

        .login-error-alert svg {
            color: #dc2626;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .form-input-field.is-invalid {
            border-color: #ef4444 !important;
            background-color: #fffafb;
        }

        .form-input-field.is-invalid:focus {
            border-color: #dc2626 !important;
        }

        .form-row {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--color-ink);
            margin-bottom: 6px;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .forgot-link {
            font-size: 13px;
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
            height: 46px;
            padding: 10px 16px;
            font-family: var(--font-body);
            font-size: 14.5px;
            color: var(--color-ink);
            background: #ffffff;
            border: 1.5px solid var(--color-surface);
            border-radius: 9px;
            outline: none;
            transition: border-color 0.15s ease;
            box-shadow: none !important;
        }

        .form-input-field:focus {
            border-color: var(--color-primary);
        }

        .form-error {
            color: #b71c1c;
            font-size: 12.5px;
            font-weight: 600;
            margin-top: 5px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
            margin-top: 4px;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 14px;
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
            height: 48px;
            background: var(--color-primary);
            color: #ffffff;
            font-family: var(--font-body);
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.1s ease;
            box-shadow: none !important;
        }

        .btn-submit-login:hover {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        .btn-submit-login:active {
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .login-page-grid {
                padding: 16px 12px;
                min-height: 100vh;
                min-height: 100dvh;
            }
            .login-pane-form {
                padding: 24px 20px;
                border-radius: 12px;
            }
            .login-heading {
                font-size: 22px;
                line-height: 1.2;
                margin-bottom: 16px;
            }
        }

        /* ─── Theme Toggle Button ─── */
        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12.5px;
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
            border-color: rgba(240, 244, 255, 0.1) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
        }

        html[data-theme="dark"] .login-back-link {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .login-heading {
            color: #F0F4FF !important;
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

        html[data-theme="dark"] .login-error-alert {
            background-color: rgba(239, 68, 68, 0.12) !important;
            color: #fca5a5 !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
        }

        html[data-theme="dark"] .login-error-alert svg {
            color: #f87171 !important;
        }

        html[data-theme="dark"] .form-input-field.is-invalid {
            border-color: #f87171 !important;
            background-color: rgba(239, 68, 68, 0.08) !important;
        }

    </style>

    <div class="login-page-grid">
        <!-- Formulir Kedinasan -->
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

                @if (session('status'))
                    <div class="status-alert animate-in">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="login-error-alert animate-in" role="alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            {{ $errors->first('email') ?: $errors->first() }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <!-- Email Address -->
                    <div class="form-row animate-in">
                        <label for="email" class="form-label">Email Kedinasan</label>
                        <input id="email" 
                               class="form-input-field @error('email') is-invalid @enderror" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="Masukkan Email">
                        @if ($errors->has('email') && $errors->first('email') !== 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.')
                            <div class="form-error">{{ $errors->first('email') }}</div>
                        @endif
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
                               class="form-input-field @if($errors->has('password') || ($errors->has('email') && $errors->first('email') === 'Email atau kata sandi yang Anda masukkan salah. Silakan periksa kembali.')) is-invalid @endif"
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
                            <span>Ingat sesi masuk</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login animate-in">
                        Masuk ke Sistem &rarr;
                    </button>
                </form>
            </div>

        </main>

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

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="SIGAP PASAR — Sistem Pengawasan, Deteksi Siber, dan Intervensi Pasar Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat">
    <title>SIGAP PASAR &mdash; Diskoperindag Prov. Sulawesi Barat</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap"
        rel="stylesheet">
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('sigap_theme');
                if (saved === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        :root {
            /* ─── Token Warna Resmi README.md Bagian 0 ─── */
            --color-primary: #2e7d32;
            /* Hutan Sulbar - Primer */
            --color-primary-dark: #1b5e20;
            /* Hutan Gelap */
            --color-accent: #4caf50;
            /* Daun */
            --color-surface: #e8f5e9;
            /* Embun */
            --color-surface-card: #ffffff;
            /* Panel Putih Bersih */
            --color-bg: #f7faf7;
            /* Kertas */
            --color-ink: #0f172a;
            /* Tinta Primer Slate-900 (Ultra Kontras) */
            --color-ink-muted: #334155;
            /* Tinta Sekunder Slate-700 (Kontras Tinggi) */

            /* Tipografi */
            --font-heading: 'Poppins', system-ui, -apple-system, sans-serif;
            --font-body: 'Poppins', system-ui, -apple-system, sans-serif;

            /* Skala Presisi README (15 / 16 / 20 / 28 / 84 px) */
            --step-display: clamp(38px, 6vw, 84px);
            --step-head: 28px;
            --step-lead: 20px;
            --step-body: 16px;
            --step-small: 15px;

            --radius-base: 8px;
            --radius-panel: 12px;
            --max-w: 1200px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            background-color: var(--color-bg);
            color: var(--color-ink);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-ink);
            font-size: var(--step-body);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ─── Motion reveal layer (transform & opacity only) ─── */
        .reveal-node {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reveal-node.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* ─── A11y Skip link ─── */
        .skip-link {
            position: absolute;
            top: -60px;
            left: 16px;
            background: var(--color-primary);
            color: #ffffff;
            padding: 10px 18px;
            font-size: var(--step-body);
            font-weight: 700;
            z-index: 1000;
            border-radius: var(--radius-base);
            transition: top 0.2s ease;
            text-decoration: none;
        }

        .skip-link:focus {
            top: 16px;
        }

        a {
            color: var(--color-primary);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .container {
            width: 100%;
            max-width: var(--max-w);
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ─── 00. Header Resmi (Kiri-Kanan Rata) ─── */
        .site-header {
            position: sticky;
            top: 0;
            background: #ffffff;
            border-bottom: 2px solid var(--color-surface);
            padding: 16px 0;
            z-index: 50;
        }

        .header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }

        .header-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .brand-titles {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title-main {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 800;
            color: var(--color-primary);
            letter-spacing: -0.03em;
        }

        .brand-title-sub {
            font-size: var(--step-small);
            font-weight: 600;
            color: var(--color-ink-muted);
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .header-nav-link {
            font-size: var(--step-body);
            font-weight: 600;
            color: var(--color-ink);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .header-nav-link:hover {
            color: var(--color-primary);
        }

        .btn-header-login {
            display: none;
            align-items: center;
            justify-content: center;
            background: var(--color-primary);
            color: #ffffff !important;
            padding: 10px 22px;
            min-height: 44px;
            font-size: var(--step-body);
            font-weight: 700;
            border-radius: var(--radius-base);
            text-decoration: none;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .btn-header-login.is-visible {
            display: inline-flex;
            animation: headerBtnFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .btn-header-login.is-hiding {
            display: inline-flex;
            animation: headerBtnFadeOut 0.18s ease forwards;
        }

        @keyframes headerBtnFadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes headerBtnFadeOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateY(-4px) scale(0.96);
            }
        }

        .btn-header-login:hover {
            background: var(--color-primary-dark);
            transform: translateY(-1px);
        }

        /* ─── 01. Hero Section ─── */
        .section-hero {
            padding: 56px 0 48px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 44px;
            align-items: center;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: var(--step-display);
            font-weight: 800;
            line-height: 0.96;
            letter-spacing: -0.04em;
            color: var(--color-ink);
            margin-bottom: 20px;
        }

        .hero-lead {
            font-size: var(--step-lead);
            line-height: 1.55;
            color: var(--color-ink-muted);
            margin-bottom: 32px;
            max-width: 560px;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-primary-hero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--color-primary);
            color: #ffffff !important;
            padding: 15px 30px;
            min-height: 52px;
            font-size: var(--step-body);
            font-weight: 700;
            border-radius: var(--radius-base);
            text-decoration: none;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .btn-primary-hero:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
        }



        /* ─── Kartu Jalur Penanganan (Hero Kanan) ─── */
        .case-card {
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: var(--radius-panel);
            padding: 28px;
            position: relative;
        }

        .case-card-header {
            margin-bottom: 20px;
        }

        .case-simulasi-title {
            font-size: var(--step-small);
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 4px;
        }

        .case-headline {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 700;
            color: var(--color-ink);
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .case-meta {
            font-size: var(--step-small);
            color: var(--color-ink-muted);
        }

        /* Stepper Vertikal dengan Garis Hijau Terisi Progresif Sekali */
        .case-stepper {
            position: relative;
            padding-left: 32px;
            margin-bottom: 24px;
        }

        .stepper-track {
            position: absolute;
            left: 9px;
            top: 10px;
            bottom: 14px;
            width: 4px;
            background: var(--color-surface);
            border-radius: 2px;
        }

        .stepper-bar-fill {
            width: 100%;
            height: 0%;
            background: var(--color-primary);
            border-radius: 2px;
            transition: height 2.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .step-item {
            position: relative;
            margin-bottom: 16px;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-dot {
            position: absolute;
            left: -32px;
            top: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-dot.final {
            background: var(--color-primary);
        }

        .step-text-title {
            font-size: var(--step-body);
            font-weight: 700;
            color: var(--color-ink);
            line-height: 1.2;
        }

        .step-text-actor {
            font-size: var(--step-small);
            color: var(--color-ink-muted);
            line-height: 1.4;
        }

        .case-result-box {
            background: var(--color-surface);
            border-radius: var(--radius-base);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .result-label {
            font-size: var(--step-small);
            font-weight: 600;
            color: var(--color-ink-muted);
        }

        .result-prices {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 800;
            color: var(--color-primary-dark);
        }

        /* ─── 02. Ringkasan Penanganan (Tabel Sederhana 1 Baris) ─── */
        .section-summary {
            padding: 24px 0 48px;
        }

        .summary-table-wrap {
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: var(--radius-panel);
            overflow: hidden;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th {
            text-align: left;
            padding: 16px 24px;
            font-size: var(--step-body);
            font-weight: 700;
            color: var(--color-ink-muted);
            border-bottom: 2px solid var(--color-surface);
            background: #fafcfa;
        }

        .summary-table td {
            padding: 18px 24px;
            font-size: var(--step-body);
            color: var(--color-ink);
        }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 800;
            color: var(--color-ink);
            display: block;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .summary-empty-note {
            display: block;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 500;
            color: var(--color-ink-muted);
            line-height: 1.4;
        }

        /* ─── 04. Alur Penanganan 4 Tahap ─── */
        .section-alur {
            padding: 48px 0;
            border-top: 2px solid var(--color-surface);
        }

        .timeline-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .timeline-card {
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: var(--radius-panel);
            padding: 24px 32px;
            display: grid;
            grid-template-columns: 140px 1fr 240px;
            gap: 24px;
            align-items: center;
        }

        .t-stage-col {
            display: flex;
            flex-direction: column;
        }

        .t-stage-num {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 800;
            color: var(--color-primary);
        }

        .t-stage-actor {
            font-size: var(--step-body);
            font-weight: 600;
            color: var(--color-ink-muted);
        }

        .t-work-col h3 {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 700;
            color: var(--color-ink);
            margin-bottom: 4px;
        }

        .t-work-col p {
            font-size: var(--step-body);
            color: var(--color-ink);
            line-height: 1.55;
        }

        .t-result-col {
            background: var(--color-surface);
            padding: 14px 18px;
            border-radius: var(--radius-base);
            font-size: var(--step-body);
            color: var(--color-primary-dark);
            font-weight: 600;
            line-height: 1.4;
        }

        /* ─── 05. Peran dan Akses (Tabel Dua Kolom) ─── */
        .section-peran {
            padding: 48px 0;
            border-top: 2px solid var(--color-surface);
        }

        .table-peran-wrap {
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: var(--radius-panel);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .table-peran {
            width: 100%;
            border-collapse: collapse;
            min-width: 640px;
        }

        .table-peran th {
            text-align: left;
            padding: 18px 24px;
            font-size: var(--step-body);
            font-weight: 700;
            color: var(--color-ink);
            background: #fafcfa;
            border-bottom: 2px solid var(--color-surface);
        }

        .table-peran td {
            padding: 22px 24px;
            font-size: var(--step-body);
            border-bottom: 1px solid var(--color-surface);
            vertical-align: top;
            line-height: 1.55;
            color: var(--color-ink);
        }

        .table-peran tr:last-child td {
            border-bottom: none;
        }

        .peran-col-header {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .peran-name {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 700;
            color: var(--color-primary);
            line-height: 1.2;
        }

        .peran-tahap-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            color: var(--color-primary-dark);
            background: var(--color-surface);
            padding: 3px 8px;
            border-radius: 4px;
            width: fit-content;
        }

        .peran-tugas-text {
            font-size: 15px;
            color: var(--color-ink);
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .peran-akses-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 14px;
        }

        .peran-akses-label {
            font-weight: 700;
            color: var(--color-ink-muted);
            margin-right: 4px;
        }

        .akses-tag {
            background: #f1f8f2;
            color: var(--color-primary-dark);
            border: 1px solid #c8e6c9;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ─── 06. Wilayah (6 Kabupaten Sulbar - Struktur Tabel Dua Kolom Bersih) ─── */
        .section-wilayah {
            padding: 48px 0;
            border-top: 2px solid var(--color-surface);
        }

        .wilayah-table-wrap {
            background: #ffffff;
            border: 2px solid var(--color-surface);
            border-radius: var(--radius-panel);
            overflow: hidden;
        }

        .wilayah-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wilayah-table th {
            text-align: left;
            padding: 18px 24px;
            font-size: var(--step-body);
            font-weight: 700;
            color: var(--color-ink);
            background: #fafcfa;
            border-bottom: 2px solid var(--color-surface);
        }

        .wilayah-table td {
            padding: 18px 24px;
            font-size: var(--step-body);
            border-bottom: 1px solid var(--color-surface);
            vertical-align: middle;
            color: var(--color-ink);
        }

        .wilayah-table tr:last-child td {
            border-bottom: none;
        }

        .wilayah-nama {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 700;
            color: var(--color-ink);
        }

        .wilayah-count {
            display: inline-flex;
            align-items: center;
            font-size: var(--step-small);
            font-weight: 700;
            color: var(--color-primary-dark);
            background: var(--color-surface);
            padding: 6px 14px;
            border-radius: 4px;
            font-variant-numeric: tabular-nums;
        }

        /* ─── 07. Jejak Audit & Akuntabilitas (Panel #e8f5e9) ─── */
        .section-audit {
            padding: 48px 0;
            border-top: 2px solid var(--color-surface);
        }

        .audit-panel {
            background: var(--color-surface);
            border-radius: var(--radius-panel);
            padding: 36px 40px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            align-items: center;
        }

        .audit-title {
            font-family: var(--font-heading);
            font-size: var(--step-head);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--color-primary-dark);
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .audit-desc {
            font-size: var(--step-body);
            color: var(--color-ink);
            line-height: 1.6;
        }

        .audit-points {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .audit-point-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: var(--step-body);
            color: var(--color-ink);
            font-weight: 600;
            line-height: 1.5;
        }

        /* ─── 08. Footer Resmi (#1b5e20) ─── */
        .site-footer {
            background: var(--color-primary-dark);
            color: #ffffff;
            padding: 56px 0 28px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 0.6fr;
            gap: 48px;
            margin-bottom: 40px;
        }

        .footer-brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .footer-logo {
            height: 48px;
            width: auto;
            object-fit: contain;
            background: #ffffff;
            border-radius: 6px;
            padding: 3px;
        }

        .footer-agency-name {
            font-family: var(--font-heading);
            font-size: var(--step-lead);
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .footer-agency-sub {
            font-size: var(--step-small);
            color: #e8f5e9;
            font-weight: 600;
        }

        .footer-address {
            font-size: var(--step-body);
            color: #e8f5e9;
            line-height: 1.6;
            max-width: 540px;
        }

        .footer-actions-col {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 18px;
        }



        .footer-bottom {
            padding-top: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: var(--step-small);
            color: #c8e6c9;
            flex-wrap: wrap;
            gap: 16px;
        }

        /* ─── Responsive Media Queries ─── */
        @media (max-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 36px;
            }

            .timeline-card {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .audit-panel {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 0 16px;
            }

            .header-nav-link {
                display: none;
            }

            .header-nav {
                gap: 10px;
            }

            .btn-header-login {
                padding: 7px 15px;
                min-height: 36px;
                font-size: 13px;
            }

            .hero-title {
                font-size: 32px;
                line-height: 1.1;
            }

            .hero-lead {
                font-size: var(--step-body);
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .audit-panel {
                padding: 24px;
            }

            .case-card {
                padding: 20px;
            }

            /* Tampilan responsif tabel di mobile */
            .summary-table-wrap {
                overflow: visible;
                border: none;
                background: transparent;
            }

            .summary-table,
            .summary-table tbody,
            .summary-table tr {
                display: block;
                width: 100%;
            }

            .summary-table thead {
                display: none;
            }

            .summary-table tbody {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .summary-table td {
                background: #ffffff;
                border: 2px solid var(--color-surface);
                border-radius: var(--radius-base);
                padding: 14px 18px;
            }

            .summary-table td::before {
                content: attr(data-label);
                display: block;
                font-family: var(--font-body);
                font-size: 14px;
                font-weight: 700;
                color: var(--color-ink-muted);
                margin-bottom: 6px;
            }

            .wilayah-table-wrap {
                overflow: visible;
                border: none;
                background: transparent;
            }

            .wilayah-table,
            .wilayah-table tbody,
            .wilayah-table tr,
            .wilayah-table td {
                display: block;
                width: 100%;
            }

            .wilayah-table thead {
                display: none;
            }

            .wilayah-table tr {
                background: #ffffff;
                border: 2px solid var(--color-surface);
                border-radius: var(--radius-base);
                margin-bottom: 10px;
                padding: 14px 18px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .wilayah-table td {
                padding: 0;
                border: none;
            }
        }

        @media (max-width: 768px) {
            .table-peran-wrap {
                overflow: visible;
                border: none;
                background: transparent;
            }

            .table-peran,
            .table-peran tbody,
            .table-peran tr,
            .table-peran td {
                display: block;
                width: 100%;
                min-width: 0;
            }

            .table-peran thead {
                display: none;
            }

            .table-peran tr {
                background: #ffffff;
                border: 2px solid var(--color-surface);
                border-radius: var(--radius-panel);
                margin-bottom: 16px;
                padding: 18px 20px;
            }

            .table-peran td {
                padding: 6px 0;
                border: none;
            }

            .table-peran td:first-child {
                border-bottom: 1px solid var(--color-surface);
                padding-bottom: 12px;
                margin-bottom: 10px;
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

        /* ─── Dark Mode Overrides for Welcome Page ─── */
        html[data-theme="dark"] {
            --color-primary: #4caf50;
            --color-primary-dark: #81c784;
            --color-accent: #66bb6a;
            --color-surface: #162335;
            --color-surface-card: #111a28;
            --color-bg: #0B0F1A;
            --color-ink: #F0F4FF;
            --color-ink-muted: #9BA8C2;
        }

        html[data-theme="dark"],
        html[data-theme="dark"] body {
            background-color: #0B0F1A !important;
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .site-header {
            background: rgba(11, 15, 26, 0.9) !important;
            border-bottom: 1px solid rgba(240, 244, 255, 0.08) !important;
            backdrop-filter: blur(12px);
        }

        html[data-theme="dark"] .header-brand .brand-title-main {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .header-brand .brand-title-sub {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .header-nav-link {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .header-nav-link:hover {
            color: #4caf50 !important;
        }

        html[data-theme="dark"] .btn-header-login {
            background: #2e7d32 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] .btn-header-login:hover {
            background: #388e3c !important;
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

        html[data-theme="dark"] .case-card {
            background: #111a28 !important;
            border-color: rgba(240, 244, 255, 0.1) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
        }

        html[data-theme="dark"] .case-headline {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .step-dot {
            background: #111a28 !important;
            border-color: #4caf50 !important;
        }

        html[data-theme="dark"] .step-text-title {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .step-text-actor {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .case-result-box {
            background: #162438 !important;
        }

        html[data-theme="dark"] .summary-table-wrap,
        html[data-theme="dark"] .table-peran-wrap,
        html[data-theme="dark"] .wilayah-table-wrap {
            background: #111a28 !important;
            border-color: rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .summary-table th,
        html[data-theme="dark"] .table-peran th,
        html[data-theme="dark"] .wilayah-table th {
            background: #162235 !important;
            color: #F0F4FF !important;
            border-bottom: 2px solid rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .summary-table td,
        html[data-theme="dark"] .table-peran td,
        html[data-theme="dark"] .wilayah-table td {
            color: #F0F4FF !important;
            border-bottom: 1px solid rgba(240, 244, 255, 0.06) !important;
        }

        html[data-theme="dark"] .stat-value {
            color: #F0F4FF !important;
        }


        html[data-theme="dark"] .timeline-card {
            background: #111a28 !important;
            border-color: rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .t-stage-actor {
            color: #9BA8C2 !important;
        }

        html[data-theme="dark"] .t-work-col h3 {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .t-work-col p {
            color: #CAD5E2 !important;
        }

        html[data-theme="dark"] .t-result-col {
            background: #162438 !important;
            color: #81c784 !important;
        }

        html[data-theme="dark"] .peran-name {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .peran-tahap-badge {
            background: #162438 !important;
            color: #81c784 !important;
        }

        html[data-theme="dark"] .peran-tugas-text {
            color: #CAD5E2 !important;
        }

        html[data-theme="dark"] .akses-tag {
            background: #172a1e !important;
            color: #81c784 !important;
            border-color: #2e7d32 !important;
        }

        html[data-theme="dark"] .wilayah-nama {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .wilayah-count {
            background: #1a2a3e !important;
            color: #81c784 !important;
        }

        html[data-theme="dark"] .audit-panel {
            background: #111a28 !important;
            border: 1px solid rgba(240, 244, 255, 0.1) !important;
        }

        html[data-theme="dark"] .audit-title {
            color: #81c784 !important;
        }

        html[data-theme="dark"] .audit-desc {
            color: #CAD5E2 !important;
        }

        html[data-theme="dark"] .audit-point-item {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .site-footer {
            background: #080c14 !important;
            border-top: 1px solid rgba(240, 244, 255, 0.08) !important;
        }



        html[data-theme="dark"] .hero-title {
            color: #F0F4FF !important;
        }

        html[data-theme="dark"] .hero-lead {
            color: #9BA8C2 !important;
        }



        html[data-theme="dark"] .section-alur,
        html[data-theme="dark"] .section-peran,
        html[data-theme="dark"] .section-wilayah,
        html[data-theme="dark"] .section-audit {
            border-top-color: rgba(240, 244, 255, 0.08) !important;
        }
    </style>
</head>

<body>

    <!-- A11y Skip link -->
    <a href="#konten-utama" class="skip-link">Lewati ke konten utama</a>

    <!-- [00. Header Resmi] -->
    <header class="site-header" role="banner">
        <div class="container header-wrap">
            <a href="/" class="header-brand" aria-label="Beranda SIGAP PASAR Sulawesi Barat">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Diskoperindag Provinsi Sulawesi Barat"
                    class="header-logo">
                <div class="brand-titles">
                    <span class="brand-title-main">SIGAP PASAR</span>
                    <span class="brand-title-sub">Diskoperindag Prov. Sulawesi Barat</span>
                </div>
            </a>

            <div style="display: flex; align-items: center; gap: 12px;">
                <nav class="header-nav" aria-label="Navigasi Halaman">
                    <a href="#alur" class="header-nav-link">Alur Penanganan</a>
                    <a href="#peran" class="header-nav-link">Peran</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-header-login" id="header-login-btn">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-header-login" id="header-login-btn">
                                Masuk
                            </a>
                        @endauth
                    @endif
                </nav>

                {{-- Tombol Ganti Tema --}}
                <button id="welcome-theme-toggle" class="theme-toggle-btn" onclick="toggleTheme()" type="button"
                    title="Ganti Tema (Gelap / Terang)" aria-label="Ganti Tema Tampilan">
                    <span id="theme-icon" style="display:inline-flex;align-items:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                        </svg>
                    </span>
                    <span id="theme-label">Tema Gelap</span>
                </button>
            </div>
        </div>
    </header>

    <main id="konten-utama">
        <!-- [01. Hero Section] -->
        <section class="section-hero" aria-label="Pengenalan SIGAP PASAR">
            <div class="container hero-grid">
                <!-- Kolom Kiri -->
                <div>
                    <h1 class="hero-title">
                        ISU VIRAL
                        DITANGANI TUNTAS
                    </h1>
                    <p class="hero-lead">
                        Membantu menghubungkan pantauan media sosial dengan pengecekan langsung di pasar di enam
                        kabupaten Sulawesi Barat.
                    </p>
                    <nav class="hero-actions" aria-label="Aksi Utama">
                        <a href="{{ route('login') }}" class="btn-primary-hero" id="hero-login-btn">
                            Masuk
                        </a>
                    </nav>
                </div>

                <!-- Kolom Kanan: Kartu Jalur Penanganan (Simulasi Radar Isu Pangan) -->
                <div>
                    <div class="case-card">
                        <div class="case-card-header">
                            <div class="case-simulasi-title">Simulasi Radar Isu Pangan</div>
                            <div class="case-headline">Harga cabai rawit naik di Pasar Mamuju</div>
                            <p class="case-meta">Pantauan linimasa media sosial &bull; 2.400 interaksi</p>
                        </div>

                        <div class="case-stepper" id="case-stepper">
                            <div class="stepper-track">
                                <div class="stepper-bar-fill" id="stepper-fill"></div>
                            </div>

                            <div class="step-item">
                                <div class="step-dot"></div>
                                <div class="step-text-title">Dilaporkan</div>
                                <div class="step-text-actor">Oleh Pengawas via pantauan linimasa</div>
                            </div>

                            <div class="step-item">
                                <div class="step-dot"></div>
                                <div class="step-text-title">Penugasan &amp; Cek Lapangan</div>
                                <div class="step-text-actor">Oleh Petugas Lapangan &amp; Admin via Geotagging GPS</div>
                            </div>

                            <div class="step-item">
                                <div class="step-dot"></div>
                                <div class="step-text-title">Langkah Intervensi</div>
                                <div class="step-text-actor">Oleh Admin bersama Tim Satgas Pangan &amp; Bulog</div>
                            </div>

                            <div class="step-item">
                                <div class="step-dot final"></div>
                                <div class="step-text-title">Selesai</div>
                                <div class="step-text-actor">Harga kembali normal sesuai HET pemerintah</div>
                            </div>
                        </div>

                        <div class="case-result-box">
                            <span class="result-label">Hasil stabilisasi harga komoditas</span>
                            <span class="result-prices">Rp85.000 &rarr; Rp62.000 / kg</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- [02. Ringkasan Penanganan - Satu Baris Tabel Sederhana] -->
        <section class="section-summary" aria-label="Ringkasan Penanganan Isu Bulan Ini">
            <div class="container">
                <div class="summary-table-wrap">
                    <table class="summary-table" role="table">
                        <thead>
                            <tr>
                                <th scope="col">Isu masuk bulan ini</th>
                                <th scope="col">Sudah selesai</th>
                                <th scope="col">Isu aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-label="Isu masuk bulan ini">
                                    <span
                                        class="stat-value">{{ isset($isuBulanIni) && $isuBulanIni > 0 ? $isuBulanIni : 0 }}</span>
                                    <span
                                        class="summary-empty-note">{{ isset($isuBulanIni) && $isuBulanIni > 0 ? 'Laporan terdata' : 'Belum ada data bulan ini' }}</span>
                                </td>
                                <td data-label="Sudah selesai">
                                    <span
                                        class="stat-value">{{ isset($selesaiCount) && $selesaiCount > 0 ? $selesaiCount : 0 }}</span>
                                    <span
                                        class="summary-empty-note">{{ isset($selesaiCount) && $selesaiCount > 0 ? 'Telah dituntaskan' : 'Belum ada data bulan ini' }}</span>
                                </td>
                                <td data-label="Isu aktif">
                                    <span
                                        class="stat-value">{{ isset($isuAktif) && $isuAktif > 0 ? $isuAktif : 0 }}</span>
                                    <span
                                        class="summary-empty-note">{{ isset($isuAktif) && $isuAktif > 0 ? 'Dalam penanganan' : 'Kondisi pasar aman' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>


        <!-- [04. Alur Penanganan 4 Tahap Sesuai Rantai Komando] -->
        <section id="alur" class="section-alur" aria-label="Alur Penanganan Isu Pasar">
            <div class="container">
                <header class="section-header-block">
                    <h2 class="section-title">Dari postingan viral sampai harga normal</h2>
                    <p class="section-desc">
                        Setiap isu pasar diproses melalui alur empat tahap yang transparan dengan tanggung jawab aktor
                        yang tegas.
                    </p>
                </header>

                <ol class="timeline-list">
                    <!-- Tahap 1 -->
                    <li class="timeline-card">
                        <div class="t-stage-col">
                            <span class="t-stage-num">Tahap 1</span>
                            <span class="t-stage-actor">Pengawas</span>
                        </div>
                        <div class="t-work-col">
                            <h3>Deteksi dan input isu</h3>
                            <p>Memantau linimasa media sosial (TikTok, X, FB, IG) dan mencatat tautan, engagement
                                interaksi, kutipan keluhan warga, komoditas, serta lokasi pasar spesifik.</p>
                        </div>
                        <div class="t-result-col">
                            Hasil: Laporan isu baru tercatat dengan skor virality otomatis.
                        </div>
                    </li>

                    <!-- Tahap 2: Gabungan Penugasan & Cek Fisik Lapangan -->
                    <li class="timeline-card">
                        <div class="t-stage-col">
                            <span class="t-stage-num">Tahap 2</span>
                            <span class="t-stage-actor">Petugas &amp; Admin</span>
                        </div>
                        <div class="t-work-col">
                            <h3>Penugasan dan cek fisik lapangan</h3>
                            <p>Petugas lapangan memilih isu atau ditugaskan oleh Admin Sistem, mendatangi lokasi pasar, melakukan check-in koordinat GPS Geolocation, mencatat harga riil aktual pedagang, mengambil foto ber-watermark waktu, dan memverifikasi ketersediaan pasokan.</p>
                        </div>
                        <div class="t-result-col">
                            Hasil: Laporan verifikasi faktual harga dan ketersediaan pasokan riil lapangan.
                        </div>
                    </li>

                    <!-- Tahap 3 (sebelumnya Tahap 4) -->
                    <li class="timeline-card">
                        <div class="t-stage-col">
                            <span class="t-stage-num">Tahap 3</span>
                            <span class="t-stage-actor">Admin &amp; Instansi</span>
                        </div>
                        <div class="t-work-col">
                            <h3>Intervensi pasar dan koordinasi</h3>
                            <p>Jika temuan terbukti anomali, Admin mengoordinasikan intervensi nyata bersama Perum Bulog
                                dan Satgas Pangan: Operasi Pasar Murah, sidak gudang, atau subsidi angkutan.</p>
                        </div>
                        <div class="t-result-col">
                            Hasil: Pelaksanaan operasi stabilisasi pasokan dan update berkala.
                        </div>
                    </li>

                    <!-- Tahap 4 (sebelumnya Tahap 5) -->
                    <li class="timeline-card">
                        <div class="t-stage-col">
                            <span class="t-stage-num">Tahap 4</span>
                            <span class="t-stage-actor">Admin Sistem</span>
                        </div>
                        <div class="t-work-col">
                            <h3>Hasil akhir dan penutupan isu</h3>
                            <p>Mengevaluasi komparasi harga Sebelum vs Sesudah intervensi, memastikan harga kembali
                                dalam ambang batas HET, serta mencatat berita acara penutupan.</p>
                        </div>
                        <div class="t-result-col">
                            Hasil: Isu resmi ditutup dengan status Selesai.
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <!-- [05. Peran dan Akses - Tabel Dua Kolom] -->
        <section id="peran" class="section-peran" aria-label="Peran Pengguna dan Hak Akses">
            <div class="container">
                <header class="section-header-block">
                    <h2 class="section-title">Siapa mengerjakan apa</h2>
                    <p class="section-desc">
                        Setiap akun memiliki pembagian wewenang yang tegas sesuai tugas pokok dan fungsinya.
                    </p>
                </header>

                <div class="table-peran-wrap">
                    <table class="table-peran" role="table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 32%;">Peran Pengguna</th>
                                <th scope="col" style="width: 68%;">Tugas Pokok & Tampilan Setelah Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="peran-col-header">
                                        <div class="peran-name">Pimpinan</div>
                                        <span class="peran-tahap-badge">Monitoring Eksekutif & Tren Harga</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="peran-tugas-text">
                                        Memantau ringkasan berkala dinamika isu pasar pangan dan perkembangan tren harga
                                        komoditas pokok setiap hari, minggu, bulan, dan tahunnya di 6 kabupaten Sulawesi
                                        Barat, didukung peta sebaran spasial titik isu untuk perumusan kebijakan
                                        intervensi strategis.
                                    </div>
                                    <div class="peran-akses-row">
                                        <span class="peran-akses-label">Akses Setelah Masuk:</span>
                                        <span class="akses-tag">Ringkasan Eksekutif</span>
                                        <span class="akses-tag">Peta Sebaran Isu Sulbar</span>
                                        <span class="akses-tag">Tren Perkembangan Harga</span>
                                        <span class="akses-tag">Filter Harian/Mingguan/Bulanan/Tahunan</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="peran-col-header">
                                        <div class="peran-name">Admin</div>
                                        <span class="peran-tahap-badge">Komando Operasional & Master Sistem</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="peran-tugas-text">
                                        Mengendalikan rantai operasional dinas secara terpadu: menerbitkan surat tugas
                                        inspeksi lapangan, memimpin agenda intervensi (operasi pasar murah, sidak
                                        distributor), mengesahkan penutupan resolusi isu, serta mengelola data master
                                        wilayah/komoditas dan manajemen akun pengguna.
                                    </div>
                                    <div class="peran-akses-row">
                                        <span class="peran-akses-label">Akses Setelah Masuk:</span>
                                        <span class="akses-tag">Dashboard Operasional</span>
                                        <span class="akses-tag">Semua Isu</span>
                                        <span class="akses-tag">Penugasan Lapangan</span>
                                        <span class="akses-tag">Antrian Intervensi Pasar</span>
                                        <span class="akses-tag">Resolusi Isu</span>
                                        <span class="akses-tag">Data Master</span>
                                        <span class="akses-tag">Manajemen User</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="peran-col-header">
                                        <div class="peran-name">Pengawas</div>
                                        <span class="peran-tahap-badge">Tahap 1: Deteksi Isu</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="peran-tugas-text">
                                        Memantau linimasa media sosial dan mencatat temuan isu pasar yang viral (tautan
                                        postingan, metrik interaksi & engagement, kalkulasi virality score, jenis
                                        komoditas, lokasi kabupaten/pasar, dan unggahan bukti tangkapan layar).
                                    </div>
                                    <div class="peran-akses-row">
                                        <span class="peran-akses-label">Akses Setelah Masuk:</span>
                                        <span class="akses-tag">Dashboard</span>
                                        <span class="akses-tag">Semua Isu</span>
                                        <span class="akses-tag">Input Isu Baru</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="peran-col-header">
                                        <div class="peran-name">Petugas</div>
                                        <span class="peran-tahap-badge">Tahap 2: Cek Fisik Lapangan</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="peran-tugas-text">
                                        Memilih langsung isu yang masuk di wilayah kerjanya, mendatangi lokasi pasar
                                        fisik, melakukan check-in koordinat GPS langsung di titik lapak, mencatat harga
                                        riil aktual pedagang, menginput hasil wawancara kondisi stok, dan mengunggah
                                        foto bukti ber-timestamp.
                                    </div>
                                    <div class="peran-akses-row">
                                        <span class="peran-akses-label">Akses Setelah Masuk:</span>
                                        <span class="akses-tag">Dashboard</span>
                                        <span class="akses-tag">Laporan Lapangan (Penugasan Saya)</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- [08. Footer #1b5e20] -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand-wrap">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Diskoperindag Provinsi Sulawesi Barat"
                            class="footer-logo">
                        <div>
                            <div class="footer-agency-name">SIGAP PASAR</div>
                            <div class="footer-agency-sub">Dinas Koperasi dan UKM, Perindustrian dan Perdagangan
                                Provinsi Sulawesi Barat</div>
                        </div>
                    </div>
                    <p class="footer-address">
                        Kompleks Perkantoran Gubernur Sulawesi Barat, Jl. H. Abd. Malik Pattana Endeng, Rangas, Kec.
                        Simboro, Kabupaten Mamuju, Sulawesi Barat 91512.
                    </p>
                </div>

                <div class="footer-actions-col">
                    <p style="font-size: var(--step-body); color: #c8e6c9;">
                        Sistem informasi resmi penanganan isu stabilitas harga dan ketersediaan bahan pangan pokok
                        daerah.
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; 2026 Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat.
                </div>
            </div>
        </div>
    </footer>

    <!-- Script Animasi Tunggal: Garis Hijau Terisi Berurutan (2.5 Detik, Sekali Saja) + Scroll Reveal Observer -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cek prefers-reduced-motion
            const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            const fillBar = document.getElementById('stepper-fill');

            if (mediaQuery.matches) {
                if (fillBar) fillBar.style.height = '100%';
            } else {
                // Beri sedikit jeda saat halaman dimuat, lalu isi dalam 2.5 detik
                setTimeout(() => {
                    if (fillBar) {
                        fillBar.style.height = '100%';
                    }
                }, 300);
            }

            // Motion layer: entrance scroll observer
            const sections = document.querySelectorAll('section');
            sections.forEach(s => s.classList.add('reveal-node'));
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) e.target.classList.add('is-revealed');
                });
            }, {
                threshold: 0.08
            });
            sections.forEach(s => obs.observe(s));
        });

        // Logika Tema Terang / Gelap
        function applyThemeUI(theme) {
            const icon = document.getElementById('theme-icon');
            const label = document.getElementById('theme-label');
            const btn = document.getElementById('welcome-theme-toggle');

            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                if (icon) {
                    icon.innerHTML =
                        '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
                }
                if (label) label.textContent = 'Tema Terang';
                if (btn) btn.setAttribute('title', 'Beralih ke Tema Terang');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                if (icon) {
                    icon.innerHTML =
                        '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
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

        // Sinkronisasi Visibilitas Tombol Masuk Header vs Tombol Masuk Main/Hero
        (function() {
            let isHiding = false;
            let hideTimeout = null;

            function updateHeaderLoginBtn() {
                const heroBtn = document.getElementById('hero-login-btn');
                const headerBtn = document.getElementById('header-login-btn');
                if (!heroBtn || !headerBtn) return;

                const header = document.querySelector('.site-header');
                const headerBottom = header ? header.getBoundingClientRect().bottom : 0;
                const heroRect = heroBtn.getBoundingClientRect();

                // Tombol di hero masih terlihat jika bagian bawahnya berada di bawah sticky header
                // dan bagian atasnya belum melewati batas bawah viewport
                const isHeroVisible = heroRect.bottom > headerBottom && heroRect.top < window.innerHeight;

                if (isHeroVisible) {
                    // Tombol di main/hero MASIH terlihat -> jangan tampilkan tombol masuk di header
                    if (headerBtn.classList.contains('is-visible') && !isHiding) {
                        isHiding = true;
                        headerBtn.classList.add('is-hiding');
                        clearTimeout(hideTimeout);
                        hideTimeout = setTimeout(() => {
                            headerBtn.classList.remove('is-visible', 'is-hiding');
                            isHiding = false;
                        }, 180);
                    } else if (!isHiding) {
                        headerBtn.classList.remove('is-visible', 'is-hiding');
                    }
                } else {
                    // Tombol di main/hero SUDAH TIDAK terlihat -> tampilkan tombol masuk di header
                    isHiding = false;
                    clearTimeout(hideTimeout);
                    headerBtn.classList.remove('is-hiding');
                    headerBtn.classList.add('is-visible');
                }
            }

            let ticking = false;

            function onScrollHandler() {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        updateHeaderLoginBtn();
                        ticking = false;
                    });
                    ticking = true;
                }
            }

            window.addEventListener('scroll', onScrollHandler, {
                passive: true
            });
            window.addEventListener('resize', onScrollHandler, {
                passive: true
            });
            document.addEventListener('DOMContentLoaded', updateHeaderLoginBtn);
            setTimeout(updateHeaderLoginBtn, 20);
        })();
    </script>
</body>

</html>

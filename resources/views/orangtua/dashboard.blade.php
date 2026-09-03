<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Orang Tua - PresensiKita</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_white.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function confirmKeluar(formId) {
        Swal.fire({
            html: `
                <div style="padding:12px 0 4px;text-align:center">
                    <div style="width:72px;height:72px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;border:2px solid #fecaca">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </div>
                    <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:10px;letter-spacing:-0.02em">Keluar dari Akun?</div>
                    <div style="font-size:13.5px;color:#64748b;line-height:1.65">Sesi Anda akan diakhiri.<br>Yakin ingin keluar?</div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                confirmButton: 'swal2-confirm',
                cancelButton:  'swal2-cancel',
                actions:       'swal2-actions',
            },
            confirmButtonColor: '#ef4444',
            cancelButtonColor: 'transparent',
            buttonsStyling: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
    </script>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ─── HEADER ─── */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-wrap: wrap;
            gap: 10px 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-brand-icon {
            width: 40px;
            height: 40px;
            background: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
        }

        .nav-brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .nav-brand-sub {
            font-size: 11px;
            font-weight: 600;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .school-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            font-size: 12px;
            color: #64748b;
        }

        .school-badge strong {
            font-size: 13px;
            color: #0f172a;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
            border-radius: 10px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background-color: #fee2e2;
            color: #dc2626;
        }

        /* ─── CONTAINER ─── */
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 28px 20px 60px;
            flex: 1;
        }

        /* ─── STUDENT INFO HERO ─── */
        .student-hero-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 28px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .student-hero-card::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .student-profile-group {
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1;
        }

        .student-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.4);
            flex-shrink: 0;
        }

        .student-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .student-meta-list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #94a3b8;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            color: #f8fafc;
            font-weight: 600;
        }

        .attendance-gauge-box {
            text-align: right;
            z-index: 1;
            flex-shrink: 0;
        }

        .gauge-number {
            font-size: 36px;
            font-weight: 800;
            color: #38bdf8;
            line-height: 1;
        }

        .gauge-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
            font-weight: 500;
        }

        /* ─── SECTION HEADER & DATE FILTER ─── */
        .filter-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
        }

        .section-title-group h3 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .section-title-group p {
            font-size: 13px;
            color: #64748b;
        }

        .date-filter-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #ffffff;
            padding: 8px 14px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .date-filter-form label {
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
        }

        .date-filter-input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            background: transparent;
            cursor: pointer;
        }

        /* ─── STATS MINI CARDS ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: var(--radius);
            padding: 16px 20px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-val {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-lbl {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        /* ─── TIMELINE PRESENSI PER JAM ─── */
        .card-main-box {
            background-color: #ffffff;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 32px;
        }

        .card-box-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-box-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jam-list-container {
            padding: 16px 24px;
        }

        .jam-row-item {
            display: grid;
            grid-template-columns: 140px 1fr 180px;
            align-items: center;
            gap: 20px;
            padding: 16px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .jam-row-item:last-child {
            border-bottom: none;
        }

        .jam-badge-time {
            display: flex;
            flex-direction: column;
        }

        .jam-number {
            font-size: 14px;
            font-weight: 800;
            color: #0284c7;
        }

        .jam-time-span {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }

        .jam-subject-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .subject-title {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        .guru-name {
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .materi-text {
            font-size: 12.5px;
            color: #334155;
            background-color: #f8fafc;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            margin-top: 4px;
            display: inline-block;
        }

        .jam-status-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-info    { background-color: #e0f2fe; color: #0369a1; }
        .badge-danger  { background-color: #fee2e2; color: #b91c1c; }

        .ket-note {
            font-size: 11.5px;
            color: #64748b;
            font-style: italic;
        }

        .empty-jam-state {
            padding: 40px 20px;
            text-align: center;
            color: #64748b;
        }

        .empty-jam-state svg {
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        /* ─── LIVE STATUS PILLS & PULSE ─── */
        .meta-pill-status-warning {
            background-color: rgba(245, 158, 11, 0.25) !important;
            border-color: rgba(245, 158, 11, 0.5) !important;
            color: #fef08a !important;
        }
        .meta-pill-status-success {
            background-color: rgba(16, 185, 129, 0.25) !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
            color: #a7f3d0 !important;
        }
        .meta-pill-status-info {
            background-color: rgba(59, 130, 246, 0.25) !important;
            border-color: rgba(59, 130, 246, 0.5) !important;
            color: #bfdbfe !important;
        }
        .meta-pill-status-normal {
            background-color: rgba(255, 255, 255, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            color: #f8fafc !important;
        }
        .status-pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .dot-warning { background-color: #f59e0b; }
        .dot-success { background-color: #10b981; }
        .dot-info    { background-color: #38bdf8; }
        .dot-normal  { background-color: #10b981; }
        .dot-pulsing {
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.8);
            animation: pulseWarning 1.6s infinite;
        }
        @keyframes pulseWarning {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.8);
            }
            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 8px rgba(245, 158, 11, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* ─── DISPEN LIVE TRACKER CARD ─── */
        .dispen-tracker-card {
            background: #ffffff;
            border-radius: var(--radius);
            padding: 24px 28px;
            margin-bottom: 28px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        .dispen-tracker-card.is-keluar {
            background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
            border-color: #fde68a;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.08);
        }
        .dispen-tracker-card.is-kembali {
            background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
            border-color: #bbf7d0;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08);
        }
        .dispen-tracker-card.is-dalam {
            background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
            border-color: #bfdbfe;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.08);
        }

        .dispen-tracker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .dispen-badge-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }
        .dispen-badge-main.keluar {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .dispen-badge-main.kembali {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .dispen-badge-main.dalam {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .dispen-title-box h3 {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .dispen-title-box p {
            font-size: 13px;
            color: #64748b;
        }

        /* ─── STEPPER TIMELINE ─── */
        .stepper-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            margin: 24px 0 20px;
            padding: 0 10px;
        }
        .stepper-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .stepper-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            background: #f1f5f9;
            color: #94a3b8;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        .stepper-step.completed .stepper-icon-box {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .stepper-step.active-warning .stepper-icon-box {
            background: #f59e0b;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
            animation: pulseStepWarning 1.6s infinite;
        }
        .stepper-step.active-info .stepper-icon-box {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        @keyframes pulseStepWarning {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }
        .stepper-step-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }
        .stepper-step-desc {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .stepper-step-time {
            font-size: 11.5px;
            font-weight: 700;
            color: #0284c7;
            margin-top: 3px;
        }
        .stepper-connector {
            flex: 1;
            height: 4px;
            background: #e2e8f0;
            position: relative;
            top: -20px;
            z-index: 1;
        }
        .stepper-connector.active {
            background: #10b981;
        }
        .stepper-connector.active-warning {
            background: linear-gradient(90deg, #10b981 0%, #f59e0b 100%);
        }

        /* ─── DETAILS GRID ─── */
        .dispen-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
        }
        .dispen-detail-box {
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        .dispen-detail-label {
            font-size: 11.5px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dispen-detail-val {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .badge-dispen {
            background-color: #f3e8ff;
            color: #7e22ce;
        }

        .btn-lihat-surat {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            background: #0284c7;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }
        .btn-lihat-surat:hover {
            background: #0369a1;
            transform: translateY(-1px);
        }

        /* ─── TABEL REKAP PER MAPEL BULANAN ─── */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .custom-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-align: left;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .custom-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .custom-table tr:hover td {
            background-color: #f8fafc;
        }

        .progress-bar-bg {
            width: 100px;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #0284c7;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .top-navbar {
                padding: 10px 14px;
            }
            .nav-right {
                gap: 10px;
            }
            .school-badge {
                display: none;
            }
            .container {
                padding: 18px 14px 48px;
            }
            .student-hero-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px 18px;
                gap: 16px;
            }
            .attendance-gauge-box {
                text-align: left;
            }
            .gauge-number {
                font-size: 30px;
            }
            .filter-header-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .date-filter-form {
                width: 100%;
                justify-content: space-between;
                box-sizing: border-box;
            }
            .jam-row-item {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .jam-status-col {
                align-items: flex-start;
            }
            .card-box-header {
                padding: 14px 16px;
            }
            .jam-list-container {
                padding: 12px 16px;
            }
            .dispen-tracker-card {
                padding: 18px 16px;
            }
            .stepper-flow {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                padding-left: 10px;
            }
            .stepper-connector {
                display: none;
            }
            .stepper-step {
                flex-direction: row;
                align-items: center;
                gap: 14px;
                text-align: left;
            }
            .stepper-icon-box {
                margin-bottom: 0;
            }
            .dispen-tracker-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .nav-brand-icon {
                width: 34px;
                height: 34px;
            }
            .nav-brand-title {
                font-size: 16px;
            }
            .nav-brand-sub {
                font-size: 10px;
            }
            .btn-logout {
                padding: 8px 12px;
                font-size: 12px;
                flex: 1;
                justify-content: center;
            }
            .nav-right {
                width: 100%;
                justify-content: flex-end;
            }
            .student-avatar {
                width: 56px;
                height: 56px;
                font-size: 22px;
                border-radius: var(--radius);
            }
            .student-name {
                font-size: 18px;
            }
            .student-meta-list {
                gap: 8px;
            }
            .meta-pill {
                font-size: 11.5px;
            }
            .section-title-group h3 {
                font-size: 16px;
            }
            .date-filter-form {
                padding: 8px 12px;
                font-size: 12px;
            }
            .stat-val {
                font-size: 19px;
            }
            .stat-icon-wrapper {
                width: 38px;
                height: 38px;
            }
            .card-box-title {
                font-size: 13.5px;
            }
            .subject-title {
                font-size: 14px;
            }
            .materi-text {
                font-size: 12px;
            }
            .badge-status {
                font-size: 12px;
                padding: 5px 12px;
            }
            .dispen-badge-main {
                font-size: 12px;
                padding: 5px 12px;
            }
            .gauge-number {
                font-size: 26px;
            }
            .gauge-label {
                font-size: 11px;
            }
        }

        /* SweetAlert Popup Image for Foto Surat */
        .swal-popup-image {
            max-width: 100% !important;
            max-height: 70vh !important;
            object-fit: contain !important;
            border-radius: 12px !important;
            margin: 12px auto !important;
        }

        /* ─── Konsistensi elemen dengan panel Admin (patokan style.css) ─── */
        .stat-card,
        .card-main-box,
        .dispen-tracker-card,
        .student-hero-card { border-radius: 12px; }
        .stat-card,
        .card-main-box,
        .dispen-tracker-card { box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04); }
        .date-filter-form { border-radius: 10px; }
        .btn-logout {
            border-radius: 10px;
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
        }
        .btn-logout:hover { background-color: #fecaca; color: #b91c1c; }
        .btn-lihat-surat {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            border-radius: 10px;
        }
        .btn-lihat-surat:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.45);
        }
        .badge-info { background-color: #dbeafe; color: #1d4ed8; }
        .custom-table th { background-color: #f8fafc; color: #475569; }
    </style>
</head>
<body>

    <!-- NAVBAR TOP -->
    <header class="top-navbar">
        <div class="nav-brand">
            <div class="nav-brand-icon">
                <img src="{{ asset('logo.png') }}" alt="Logo PresensiKita" style="width:100%;height:100%;object-fit:contain;border-radius:9px;">
            </div>
            <div>
                <div class="nav-brand-title">PresensiKita</div>
                <div class="nav-brand-sub">Platform Absensi Digital</div>
            </div>
        </div>

        <div class="nav-right">
            <div class="school-badge">
                <strong>{{ $namaSekolah }}</strong>
                <span>TA {{ $tahunAjaran->tahun_ajaran ?? '' }} ({{ $tahunAjaran->semester ?? '' }})</span>
            </div>

            <button type="button" onclick="bukaModalLaporOrangTua()" class="btn-logout" style="background-color: #dbeafe; color: #2563eb; border-color: #93c5fd; margin-right: 8px;" title="Laporkan Kendala">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Lapor Admin
            </button>

            <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none">
                @csrf
            </form>
            <button type="button" class="btn-logout" title="Keluar dari sistem" onclick="confirmKeluar('logout-form')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Keluar
            </button>
        </div>
    </header>

    <!-- CONTAINER UTAMA -->
    <div class="container">

        <!-- STUDENT PROFILE HERO -->
        <div class="student-hero-card">
            <div class="student-profile-group">
                <div class="student-avatar">
                    {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
                </div>
                <div>
                    <h2 class="student-name">{{ $siswa->nama_siswa }}</h2>
                    <div class="student-meta-list">
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="13" y2="12"/></svg>
                            NISN: {{ $siswa->nisn }}
                        </span>
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </span>
                        <span class="meta-pill">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>

                        @if($dispenHariIni)
                            @if($dispenHariIni->sudahKembali())
                                <span class="meta-pill meta-pill-status-success">
                                    <span class="status-pulse-dot dot-success"></span>
                                    Dispen: Sudah Kembali ke Sekolah ({{ $dispenHariIni->waktu_masuk->format('H:i') }} WIB)
                                </span>
                            @elseif($dispenHariIni->sedangKeluarSekolah() || (!empty($dispenHariIni->waktu_keluar) && !$dispenHariIni->sudahKembali()))
                                <span class="meta-pill meta-pill-status-warning">
                                    <span class="status-pulse-dot dot-warning dot-pulsing"></span>
                                    Dispen: Sedang di Luar Sekolah (Keluar {{ $dispenHariIni->waktu_keluar->format('H:i') }} WIB)
                                </span>
                            @else
                                <span class="meta-pill meta-pill-status-info">
                                    <span class="status-pulse-dot dot-info"></span>
                                    Dispen di Dalam Sekolah
                                </span>
                            @endif
                        @else
                            <span class="meta-pill meta-pill-status-normal">
                                <span class="status-pulse-dot dot-normal"></span>
                                Status: Pembelajaran Normal di Sekolah
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="attendance-gauge-box">
                <div class="gauge-number">{{ $pctHadirBulan }}%</div>
                <div class="gauge-label">Tingkat Kehadiran Bulan Ini</div>
            </div>
        </div>

        <!-- STATUS DISPENSASI ANAK HARI INI (keluar sekolah atau di dalam sekolah) -->
        @if($dispenHariIni)
            @php
                $isKembali = $dispenHariIni->sudahKembali();
                $isKeluar = $dispenHariIni->sedangKeluarSekolah() || (!$isKembali && !empty($dispenHariIni->waktu_keluar));
                $isDalam = !$isKembali && !$isKeluar;
                $suratUrl = $dispenHariIni->fotoSuratUrl();
                $durasi = $dispenHariIni->durasiKeluar();
            @endphp
            <div class="dispen-tracker-card {{ $isKembali ? 'is-kembali' : ($isKeluar ? 'is-keluar' : 'is-dalam') }}">
                <div class="dispen-tracker-header">
                    <div class="dispen-title-box">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap">
                            @if($isKembali)
                                <span class="dispen-badge-main kembali">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    SUDAH KEMBALI KE SEKOLAH
                                </span>
                                <span style="font-size:13px; color:#15803d; font-weight:700">Masuk: {{ $dispenHariIni->waktu_masuk->format('H:i') }} WIB</span>
                            @elseif($isKeluar)
                                <span class="dispen-badge-main keluar">
                                    <span class="status-pulse-dot dot-warning dot-pulsing"></span>
                                    SEDANG DI LUAR SEKOLAH
                                </span>
                                <span style="font-size:13px; color:#b45309; font-weight:700">Keluar: {{ $dispenHariIni->waktu_keluar->format('H:i') }} WIB @if($durasi) (± {{ $durasi }} di luar) @endif</span>
                            @else
                                <span class="dispen-badge-main dalam">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                    DISPEN DI DALAM SEKOLAH
                                </span>
                                <span style="font-size:13px; color:#1e40af; font-weight:700">Berada di Lingkungan Sekolah</span>
                            @endif
                        </div>
                        <h3>
                            @if($isKembali)
                                {{ $siswa->nama_siswa }} telah kembali ke sekolah
                            @elseif($isKeluar)
                                {{ $siswa->nama_siswa }} sedang berada di luar sekolah
                            @else
                                {{ $siswa->nama_siswa }} sedang menjalankan dispensasi di sekolah
                            @endif
                        </h3>
                        <p>
                            @if($isKembali)
                                Siswa telah menyelesaikan dispensasi dan tercatat masuk kembali melalui pos satpam sekolah pada pukul {{ $dispenHariIni->waktu_masuk->format('H:i') }} WIB @if($durasi) (Total waktu dispen: {{ $durasi }}) @endif.
                            @elseif($isKeluar)
                                Siswa telah diizinkan keluar melalui pos satpam sekolah pada pukul {{ $dispenHariIni->waktu_keluar->format('H:i') }} WIB untuk keperluan dispensasi dan belum tercatat kembali.
                            @else
                                Siswa memegang izin dispensasi resmi untuk kegiatan di dalam area sekolah (belum atau tidak meninggalkan gerbang sekolah).
                            @endif
                        </p>
                    </div>

                    <div>
                        @if($suratUrl)
                            <button type="button" class="btn-lihat-surat" data-url="{{ $suratUrl }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Lihat Foto Surat Dispen</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- STEPPER ALUR DISPENSASI -->
                <div class="stepper-flow">
                    <!-- Step 1: Dispen Diterbitkan -->
                    <div class="stepper-step completed">
                        <div class="stepper-icon-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div class="stepper-step-title">Izin Disetujui</div>
                        <div class="stepper-step-desc">Piket: {{ $dispenHariIni->guruPiket->nama_guru ?? 'Guru Piket' }}</div>
                        <div class="stepper-step-time">{{ \Carbon\Carbon::parse($dispenHariIni->tanggal_dispen)->format('d M Y') }}</div>
                    </div>

                    <div class="stepper-connector {{ $isKeluar ? 'active-warning' : ($isKembali ? 'active' : '') }}"></div>

                    <!-- Step 2: Keluar Gerbang Sekolah -->
                    <div class="stepper-step {{ $isKembali ? 'completed' : ($isKeluar ? 'active-warning' : 'active-info') }}">
                        <div class="stepper-icon-box">
                            @if($isKembali)
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            @elseif($isKeluar)
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            @else
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            @endif
                        </div>
                        <div class="stepper-step-title">Keluar Gerbang</div>
                        @if($dispenHariIni->waktu_keluar)
                            <div class="stepper-step-desc">Pos Satpam</div>
                            <div class="stepper-step-time">{{ $dispenHariIni->waktu_keluar->format('H:i') }} WIB</div>
                        @else
                            <div class="stepper-step-desc">Di Dalam Sekolah</div>
                            <div class="stepper-step-time" style="color:#64748b;">(Tidak Keluar)</div>
                        @endif
                    </div>

                    <div class="stepper-connector {{ $isKembali ? 'active' : '' }}"></div>

                    <!-- Step 3: Masuk Kembali ke Sekolah -->
                    <div class="stepper-step {{ $isKembali ? 'completed' : '' }}">
                        <div class="stepper-icon-box">
                            @if($isKembali)
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            @elseif($isKeluar)
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            @else
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            @endif
                        </div>
                        <div class="stepper-step-title">Kembali ke Sekolah</div>
                        @if($isKembali)
                            <div class="stepper-step-desc">Tercatat Masuk</div>
                            <div class="stepper-step-time" style="color:#15803d;">{{ $dispenHariIni->waktu_masuk->format('H:i') }} WIB</div>
                        @elseif($isKeluar)
                            <div class="stepper-step-desc" style="color:#b45309; font-weight:700">Sedang di Luar</div>
                            <div class="stepper-step-time" style="color:#b45309;">Menunggu Masuk</div>
                        @else
                            <div class="stepper-step-desc">Lingkungan Sekolah</div>
                            <div class="stepper-step-time" style="color:#0284c7;">Aman di Sekolah</div>
                        @endif
                    </div>
                </div>

                <!-- DETAILS GRID -->
                <div class="dispen-details-grid">
                    <div class="dispen-detail-box">
                        <div class="dispen-detail-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                            Alasan / Keperluan
                        </div>
                        <div class="dispen-detail-val">{{ $dispenHariIni->alasan }}</div>
                    </div>

                    <div class="dispen-detail-box">
                        <div class="dispen-detail-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Guru Piket Penerbit
                        </div>
                        <div class="dispen-detail-val">{{ $dispenHariIni->guruPiket->nama_guru ?? 'Guru Piket Sekolah' }}</div>
                    </div>

                    <div class="dispen-detail-box">
                        <div class="dispen-detail-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Waktu Keluar
                        </div>
                        <div class="dispen-detail-val">
                            @if($dispenHariIni->waktu_keluar)
                                <span style="color:#d97706;">{{ $dispenHariIni->waktu_keluar->format('H:i') }} WIB</span>
                            @else
                                <span style="color:#64748b;">Tidak Keluar Gerbang</span>
                            @endif
                        </div>
                    </div>

                    <div class="dispen-detail-box">
                        <div class="dispen-detail-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Waktu Kembali
                        </div>
                        <div class="dispen-detail-val">
                            @if($dispenHariIni->waktu_masuk)
                                <span style="color:#15803d;">{{ $dispenHariIni->waktu_masuk->format('H:i') }} WIB</span>
                            @elseif($dispenHariIni->waktu_keluar)
                                <span style="color:#d97706; font-weight:800;">Belum Kembali</span>
                            @else
                                <span style="color:#0284c7;">Di Dalam Sekolah</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- FILTER & SUMMARY HARI INI -->
        <div class="filter-header-bar">
            <div class="section-title-group">
                <h3>Detail Presensi Kehadiran Per Jam & Mapel</h3>
                <p>Menampilkan jadwal dan presensi untuk hari <strong>{{ $hariIndo }}, {{ date('d F Y', strtotime($tanggal)) }}</strong></p>
            </div>

            <form method="GET" action="{{ route('orangtua.index') }}" class="date-filter-form">
                <label for="tanggal">Pilih Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}" class="date-filter-input" onchange="this.form.submit()">
            </form>
        </div>

        <!-- MINI STATS CARDS HARI INI -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #dcfce7; color: #16a34a;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Hadir'] }} Jam</div>
                    <div class="stat-lbl">Hadir</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #f3e8ff; color: #7e22ce;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Dispen'] ?? 0 }} Jam</div>
                    <div class="stat-lbl">Dispensasi</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #fef3c7; color: #d97706;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Sakit'] }} Jam</div>
                    <div class="stat-lbl">Sakit</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #dbeafe; color: #1d4ed8;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Izin'] }} Jam</div>
                    <div class="stat-lbl">Izin</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: #fee2e2; color: #dc2626;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div class="stat-val">{{ $statHarian['Alpa'] }} Jam</div>
                    <div class="stat-lbl">Alpa</div>
                </div>
            </div>
        </div>

        <!-- TIMELINE PER JAM & MAPEL -->
        <div class="card-main-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Jadwal & Presensi Jam Pelajaran Hari {{ $hariIndo }}
                </div>
            </div>

            <div class="jam-list-container">
                @forelse($presensiPerJam as $p)
                <div class="jam-row-item">
                    <div class="jam-badge-time">
                        <span class="jam-number">Jam Ke-{{ $p['jam_ke'] >= 100 ? $p['jam_ke'] - 100 : $p['jam_ke'] }}</span>
                        <span class="jam-time-span">{{ $p['jam_mulai'] }} - {{ $p['jam_selesai'] }} WIB</span>
                    </div>

                    <div class="jam-subject-info">
                        <div class="subject-title">
                            [{{ $p['kode_mapel'] }}] {{ $p['nama_mapel'] }}
                        </div>
                        <div class="guru-name">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Guru Pengampu: {{ $p['nama_guru'] }}
                        </div>
                        @if($p['materi'] !== '-')
                        <div>
                            <span class="materi-text">Materi: {{ $p['materi'] }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="jam-status-col">
                        <span class="badge-status {{ $p['badge_class'] }}">
                            @if($p['status'] === 'Hadir')
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                            {{ $p['status_label'] }}
                        </span>
                        @if($p['keterangan'] !== '-')
                        <span class="ket-note">Ket: {{ $p['keterangan'] }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="empty-jam-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p style="font-weight:700; font-size:15px; color:#334155;">Tidak Ada Jadwal Pelajaran</p>
                    <p style="font-size:13px; margin-top:4px;">Tidak terdapat jadwal kegiatan belajar mengajar pada hari {{ $hariIndo }}.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- REKAPITULASI BULANAN PER MAPEL -->
        <div class="card-main-box">
            <div class="card-box-header">
                <div class="card-box-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Rekapitulasi Kehadiran Per Mata Pelajaran (Bulan {{ date('F Y', strtotime($tanggal)) }})
                </div>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mata Pelajaran</th>
                            <th>Total Jam</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerMapel as $idx => $rm)
                        <tr>
                            <td style="font-weight:700;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $rm['nama_mapel'] }}</strong>
                                <span style="font-size:11px; color:#64748b; display:block;">Kode: {{ $rm['kode_mapel'] }}</span>
                            </td>
                            <td>{{ $rm['total_jam'] }} Jam</td>
                            <td><span style="color:#15803d; font-weight:700;">{{ $rm['hadir'] }}</span></td>
                            <td><span style="color:#b45309; font-weight:700;">{{ $rm['sakit'] }}</span></td>
                            <td><span style="color:#0369a1; font-weight:700;">{{ $rm['izin'] }}</span></td>
                            <td><span style="color:#b91c1c; font-weight:700;">{{ $rm['alpa'] }}</span></td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ $rm['persentase'] }}%;"></div>
                                </div>
                                <strong>{{ $rm['persentase'] }}%</strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:#64748b; padding:24px;">Belum ada data rekap presensi mata pelajaran pada bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIWAYAT DISPENSASI & SURAT IZIN SISWA -->
        <div class="card" style="padding:22px 24px; margin-top:28px; background:#fff; border-radius:var(--radius); border:1px solid #e2e8f0; box-shadow:var(--shadow)">
            <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
                <div>
                    <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin:0">Riwayat Dispensasi &amp; Surat Izin Siswa</h3>
                    <p style="font-size:12.5px; color:#64748b; margin:2px 0 0 0">Riwayat pengajuan dispensasi, sakit, dan izin beserta status keluar-masuk sekolah dan bukti surat resmi</p>
                </div>
            </div>

            <div style="overflow-x:auto">
                <table class="data-table" style="width:100%; min-width:800px; border-collapse:collapse">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0">
                            <th style="padding:10px 14px; text-align:left; font-size:12.5px; color:#475569">Tanggal</th>
                            <th style="padding:10px 14px; text-align:center; font-size:12.5px; color:#475569">Jenis Presensi</th>
                            <th style="padding:10px 14px; text-align:left; font-size:12.5px; color:#475569">Alasan / Keperluan</th>
                            <th style="padding:10px 14px; text-align:left; font-size:12.5px; color:#475569">Guru Piket</th>
                            <th style="padding:10px 14px; text-align:center; font-size:12.5px; color:#475569">Status Keluar-Masuk</th>
                            <th style="padding:10px 14px; text-align:center; font-size:12.5px; color:#475569">Foto Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatDispen as $rd)
                            <tr style="border-bottom:1px solid #f1f5f9">
                                <td style="padding:12px 14px; font-size:13px"><strong>{{ \Carbon\Carbon::parse($rd->tanggal_dispen)->format('d-m-Y') }}</strong></td>
                                <td style="padding:12px 14px; text-align:center">
                                    @if($rd->jenis_absen === 'S')
                                        <span class="badge badge-warning" style="font-size:12px; padding:4px 10px">Sakit</span>
                                    @elseif($rd->jenis_absen === 'I')
                                        <span class="badge badge-info" style="font-size:12px; padding:4px 10px">Izin</span>
                                    @elseif($rd->jenis_absen === 'D')
                                        <span class="badge" style="font-size:12px; background:#ede9fe; color:#7c3aed; padding:4px 10px">Dispensasi</span>
                                    @else
                                        <span class="badge" style="font-size:12px; background:#f1f5f9; color:#475569; padding:4px 10px">{{ $rd->jenis_absen }}</span>
                                    @endif
                                </td>
                                <td style="padding:12px 14px; font-size:13px; color:#334155">{{ $rd->alasan }}</td>
                                <td style="padding:12px 14px; font-size:12.5px; color:#475569">{{ $rd->guruPiket->nama_guru ?? '-' }}</td>
                                <td style="padding:12px 14px; text-align:center">
                                    @if($rd->sudahKembali())
                                        <span class="badge badge-success" style="font-size:12px; background:#dcfce7; color:#15803d; padding:4px 10px; display:inline-flex; align-items:center; gap:4px">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            Sudah Masuk ({{ $rd->waktu_masuk->format('H:i') }})
                                        </span>
                                    @elseif($rd->sedangKeluarSekolah() || (!empty($rd->waktu_keluar) && $rd->jenis_absen === 'D'))
                                        <span class="badge badge-warning" style="font-size:12px; background:#fef3c7; color:#b45309; padding:4px 10px; display:inline-flex; align-items:center; gap:4px">
                                            <span class="status-pulse-dot dot-warning" style="background:#f59e0b; width:6px; height:6px;"></span>
                                            Di Luar (Keluar: {{ $rd->waktu_keluar->format('H:i') }})
                                        </span>
                                    @elseif($rd->jenis_absen === 'D')
                                        <span class="badge badge-info" style="font-size:12px; background:#dbeafe; color:#1d4ed8; padding:4px 10px">Di Dalam Sekolah</span>
                                    @else
                                        <span style="color:#94a3b8; font-size:12px">-</span>
                                    @endif
                                </td>
                                <td style="padding:12px 14px; text-align:center">
                                    @if($rd->fotoSuratUrl())
                                        <button type="button" class="btn-lihat-surat" data-url="{{ $rd->fotoSuratUrl() }}" style="font-size:12px; font-weight:700; color:#2563eb; background:#dbeafe; border:1.5px solid #93c5fd; padding:5px 12px; border-radius:7px; cursor:pointer">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            Lihat Surat
                                        </button>
                                    @else
                                        <span style="color:#94a3b8; font-size:12px">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; color:#64748b; padding:22px">
                                    Belum ada data riwayat dispensasi atau surat izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
    function showSuratPopup(url) {
        if (!url) return;
        Swal.fire({
            title: 'Foto Surat Izin / Dispensasi',
            imageUrl: url,
            imageAlt: 'Foto Surat',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#0284c7',
            customClass: {
                image: 'swal-popup-image'
            }
        });
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-lihat-surat');
        if (btn) {
            e.preventDefault();
            showSuratPopup(btn.dataset.url);
        }
    });

    function bukaModalLaporOrangTua() {
        Swal.fire({
            title: 'Kirim Laporan / Pengaduan Ke Admin',
            html: `
                <div style="text-align: left; font-family: inherit;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Nama Orang Tua / Pelapor</label>
                        <input type="text" id="swal-nama-pelapor" class="swal2-input" value="Orang Tua dari {{ $siswa->nama_siswa }}" style="margin: 0; width: 100%; font-size: 14px; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;" readonly>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Judul Laporan</label>
                        <input type="text" id="swal-judul-laporan" class="swal2-input" placeholder="Contoh: Masalah Absensi / Kendala Akun" style="margin: 0; width: 100%; font-size: 14px; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 700; color: #334155; display: block; margin-bottom: 6px;">Rincian Masalah</label>
                        <textarea id="swal-isi-laporan" class="swal2-textarea" placeholder="Jelaskan kendala Anda secara rinci..." style="margin: 0; width: 100%; height: 100px; font-size: 14px; font-family: inherit; box-sizing: border-box; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim Laporan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            preConfirm: () => {
                const judul = Swal.getPopup().querySelector('#swal-judul-laporan').value;
                const isi = Swal.getPopup().querySelector('#swal-isi-laporan').value;
                if (!judul || !isi) {
                    Swal.showValidationMessage(`Judul dan rincian laporan tidak boleh kosong`);
                }
                return { judul: judul, isi_laporan: isi }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('role_pelapor', 'Orang Tua');
                formData.append('nama_pelapor', 'Orang Tua dari {{ $siswa->nama_siswa }}');
                formData.append('judul', result.value.judul);
                formData.append('isi_laporan', result.value.isi_laporan);

                fetch('{{ route("laporan.public.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            confirmButtonColor: '#2563eb'
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal mengirim laporan.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }
    </script>

</body>
</html>

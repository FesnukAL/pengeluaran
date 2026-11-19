<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Catatan Keuangan & Tugas') }} - Masuk</title>

    <link rel="shortcut icon" href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e" type="image/x-icon">

    <!-- Bootstrap 5 & Bootstrap Icons (Cloud CDN agar selalu termuat 100% di cloud) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Mazer Fallback Assets -->
    <link rel="stylesheet" href="{{ secure_asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('assets/compiled/css/app-dark.css') }}">
    <script src="{{ secure_asset('assets/static/js/initTheme.js') }}"></script>
    {{-- Terapkan tema dari localStorage sebelum render (cegah flash) --}}
    <script>
        (function() {
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>
    @livewireStyles
    <style>
        body {
            background: linear-gradient(135deg, #f0f4fd 0%, #e5edfb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        html[data-bs-theme="dark"] body {
            background: linear-gradient(135deg, #151521 0%, #1e1e2d 100%);
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(67, 94, 190, 0.08), 0 5px 15px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(67, 94, 190, 0.1);
            overflow: hidden;
        }

        html[data-bs-theme="dark"] .auth-card {
            background: #1e1e2d;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .auth-header {
            padding: 2.5rem 2.5rem 1rem 2.5rem;
            text-align: center;
        }

        .auth-body {
            padding: 1rem 2.5rem 2.5rem 2.5rem;
        }

        @media (max-width: 576px) {
            .auth-header {
                padding: 2rem 1.5rem 0.5rem 1.5rem;
            }
            .auth-body {
                padding: 1rem 1.5rem 2rem 1.5rem;
            }
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(67, 94, 190, 0.1);
            color: #435ebe;
            padding: 0.5rem 1.15rem;
            border-radius: 50rem;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        html[data-bs-theme="dark"] .brand-badge {
            background: rgba(67, 94, 190, 0.25);
            color: #8da2fb;
        }

        /* Input Styling Presisi Mazer */
        .input-group-modern {
            position: relative;
            margin-bottom: 1.25rem;
        }

        .input-group-modern .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #8c9096;
            font-size: 1.15rem;
            pointer-events: none;
            z-index: 4;
            transition: color 0.2s;
        }

        .input-group-modern .form-control {
            padding: 0.75rem 1rem 0.75rem 2.85rem !important;
            font-size: 0.95rem;
            border-radius: 0.65rem;
            border: 1px solid #dce7f6;
            background-color: #fbfdff;
            transition: all 0.2s ease-in-out;
            width: 100%;
        }

        html[data-bs-theme="dark"] .input-group-modern .form-control {
            background-color: #171723;
            border-color: #2b2b3d;
            color: #e2e8f0;
        }

        .input-group-modern .form-control:focus {
            border-color: #435ebe;
            background-color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(67, 94, 190, 0.18);
        }

        html[data-bs-theme="dark"] .input-group-modern .form-control:focus {
            background-color: #171723;
            border-color: #708aff;
            box-shadow: 0 0 0 0.25rem rgba(112, 138, 255, 0.2);
        }

        .input-group-modern .form-control:focus + .input-icon,
        .input-group-modern:focus-within .input-icon {
            color: #435ebe;
        }

        .btn-modern-primary {
            background: #435ebe;
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 0.85rem;
            border-radius: 0.65rem;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(67, 94, 190, 0.35);
            transition: all 0.2s ease;
        }

        .btn-modern-primary:hover {
            background: #364b98;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(67, 94, 190, 0.45);
        }

        .btn-modern-primary:active {
            transform: translateY(0);
        }
    </style>
</head>

<body>
    {{-- Tombol Toggle Dark/Light Mode --}}
    <button id="btn-theme-toggle" onclick="toggleTheme()" title="Ganti Mode Gelap/Terang"
        style="position:fixed;top:1rem;right:1rem;z-index:9999;border:none;border-radius:50rem;
               width:42px;height:42px;cursor:pointer;font-size:1.2rem;
               background:rgba(255,255,255,0.85);box-shadow:0 2px 10px rgba(0,0,0,0.15);
               backdrop-filter:blur(6px);transition:all 0.2s;">
        🌙
    </button>

    <div class="auth-card">
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inisialisasi tema dari localStorage
        (function() {
            var saved = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', saved);
            var btn = document.getElementById('btn-theme-toggle');
            if (btn) btn.textContent = saved === 'dark' ? '☀️' : '🌙';
        })();

        function toggleTheme() {
            var current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            var btn = document.getElementById('btn-theme-toggle');
            if (btn) btn.textContent = next === 'dark' ? '☀️' : '🌙';
            // Sinkron style body background
            if (next === 'dark') {
                btn.style.background = 'rgba(30,30,45,0.85)';
            } else {
                btn.style.background = 'rgba(255,255,255,0.85)';
            }
        }
    </script>
    @livewireScripts
</body>

</html>

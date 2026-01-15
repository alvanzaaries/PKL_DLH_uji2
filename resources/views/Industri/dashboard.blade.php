<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dinas Lingkungan Hidup dan Kehutanan</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: rgb(9, 58, 28); /* Navy Dark */
            --accent: #15803d;  /* Forest Green */
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Top Bar Professional */
        .top-utility-bar {
            background: #f1f5f9;
            padding: 8px 5%;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navigation */
        nav {
            background: var(--white);
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .logo-area img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-right: 12px;
            display: inline-block;
            vertical-align: middle;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 32px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .btn-login {
            background: var(--primary);
            color: white !important;
            padding: 8px 20px;
            border-radius: 6px;
        }

        /* Hero Section - Clean */
        .hero {
            padding: 60px 0;
            background: var(--white);
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 40px;
            text-align: center;
        }

        .hero-title {
            font-size: 32px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-subtitle {
            color: #64748b;
            font-size: 16px;
        }

        /* Alert Banner - Minimalist */
        .alert-mini {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
        }

        .stat-card:hover {
            border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            display: block;
        }

        .stat-desc {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Highlight Section */
        .total-banner {
            background: var(--primary);
            color: var(--white);
            padding: 40px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .total-text h3 { font-size: 18px; opacity: 0.9; font-weight: 400; }
        .total-text h2 { font-size: 48px; font-weight: 800; }

        /* Carousel Content */
        .section-header {
            margin-bottom: 24px;
            border-left: 4px solid var(--accent);
            padding-left: 15px;
        }

        .carousel-box {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .slide-placeholder {
            height: 350px;
            background: #d6ebdd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-weight: 500;
        }

        /* Footer */
        footer {
            background: var(--white);
            border-top: 1px solid #e2e8f0;
            padding: 40px 0;
            margin-top: 60px;
            font-size: 14px;
            color: #64748b;
        }

        .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .total-banner { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="container nav-content">
            <div class="logo-area">
                <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah" class="site-logo">
                <span class="logo-text">Dinas Lingkungan Hidup dan Kehutanan</span>
            </div>
            <ul class="nav-links">
                <li><a href="{{ route('industri.dashboard') }}">Beranda</a></li>
                <li><a href="#">Data Publik</a></li>
                <li><a href="#">Regulasi</a></li>
                @guest
                <li><a href="{{ route('login') }}" class="btn-login">Portal Login</a></li>
                @else
                <li style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-size: 14px; font-weight: 500; color: var(--text-main);">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: #fee2e2; color: #991b1b; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.2s;">
                            Logout
                        </button>
                    </form>
                </li>
                @endguest
            </ul>
        </div>
        
        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert-mini alert-success" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: 1px solid #e2e8f0;">
            <div class="container" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="alert-mini alert-error" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: 1px solid #e2e8f0;">
            <div class="container" style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif
    </nav>

    <script>
        // Auto-hide alerts after 1.5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-mini');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(function(alert) {
                        alert.style.transition = 'opacity 0.3s ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 300);
                    });
                }, 1500);
            }
        });
    </script>

    <header class="hero">
        <div class="container">
            <h1 class="hero-title">Sistem Informasi Database Industri</h1>
            <p class="hero-subtitle">Pengelolaan Hasil Hutan Provinsi Indonesia</p>
        </div>
    </header>

    <main class="container">


        <div class="stats-grid">
            <a href="{{ route('industri-primer.index') }}" class="stat-card">
                <div class="stat-label">Industri Primer</div>
                <span class="stat-number">{{ number_format($statistics['primer_pbphh']) }}</span>
                <div class="stat-desc">Izin PBPHH Aktif • Klik untuk lihat data</div>
            </a>
            <a href="{{ route('industri-sekunder.index') }}" class="stat-card">
                <div class="stat-label">Industri Sekunder</div>
                <span class="stat-number">{{ number_format($statistics['sekunder_pbui']) }}</span>
                <div class="stat-desc">Izin PBUI Aktif</div>
            </a>
            <a href="{{ route('tptkb.index') }}" class="stat-card">
                <div class="stat-label">Pengolahan TPT-KB</div>
                <span class="stat-number">{{ number_format($statistics['tpt_kb']) }}</span>
                <div class="stat-desc">Terdaftar di Database</div>
            </a>
            <a href="{{ route('perajin.index') }}" class="stat-card">
                <div class="stat-label">Industri Perajin</div>
                <span class="stat-number">{{ number_format($statistics['perajin']) }}</span>
                <div class="stat-desc">Usaha Mikro & Kecil</div>
            </a>
        </div>

        <div class="total-banner">
            <div class="total-text">
                <h3>Total Industri Terintegrasi</h3>
                <h2>{{ number_format($statistics['total_industri']) }} <span style="font-size: 20px; font-weight: 300;">Unit Usaha</span></h2>
            </div>
            <div class="total-action">
                <button style="padding: 12px 24px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer;">Unduh Laporan Tahunan</button>
            </div>
        </div>

        <section>
            <div class="section-header">
                <h2 style="font-size: 20px; color: var(--primary);">Dokumentasi Kegiatan Industri</h2>
            </div>
            <div class="carousel-box">
                <div class="slide-placeholder">
                    [ Area Dokumentasi Visual & Galeri Industri ]
                </div>
            </div>
        </section>

    </main>

    <footer>
        <div class="container footer-grid">
            <div>
                <strong>SIIPPHH</strong><br>
                &copy; 2026 Hak Cipta Dilindungi Undang-Undang
            </div>
            <div style="text-align: right;">
                <a href="#" style="color: #64748b; margin-left: 20px; text-decoration: none;">Kebijakan Data</a>
                <a href="#" style="color: #64748b; margin-left: 20px; text-decoration: none;">Kontak Kami</a>
            </div>
        </div>
    </footer>

</body>
</html>     
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Pelaporan Hasil Hutan - DLHK Jawa Tengah</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F3F4F6;
            color: #1F2937;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .landing-container {
            background: white;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #E5E7EB;
            min-height: 600px;
            max-width: 1400px;
            width: 95%;
            margin: 2rem auto;
        }

        .landing-flex {
            display: flex;
            flex-direction: column;
            min-height: 600px;
        }

        @media (min-width: 1024px) {
            .landing-flex {
                flex-direction: row;
            }
        }

        .landing-content {
            width: 100%;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            order: 2;
        }

        @media (min-width: 768px) {
            .landing-content {
                padding: 3rem 4rem;
            }
        }

        @media (min-width: 1024px) {
            .landing-content {
                width: 50%;
                padding: 4rem;
                order: 1;
            }
        }

        .landing-logo {
            width: 100%;
            background: linear-gradient(135deg, #064E3B 0%, #10B981 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            order: 1;
            padding: 3rem;
            min-height: 300px;
        }

        @media (min-width: 1024px) {
            .landing-logo {
                width: 50%;
                order: 2;
                min-height: 600px;
            }
        }

        .landing-logo::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .landing-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            letter-spacing: -0.025em;
        }

        @media (min-width: 640px) {
            .landing-title {
                font-size: 3rem;
            }
        }

        @media (min-width: 768px) {
            .landing-title {
                font-size: 3.75rem;
            }
        }

        .landing-title-highlight {
            display: block;
            color: #10B981;
        }

        .landing-description {
            margin-top: 1rem;
            font-size: 1.125rem;
            color: #6B7280;
            margin-bottom: 2rem;
            line-height: 1.75;
        }

        .landing-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .landing-buttons {
                flex-direction: row;
            }
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2rem;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            color: white;
            background: #10B981;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        @media (min-width: 768px) {
            .btn-primary {
                padding: 1.25rem 2rem;
                font-size: 1.125rem;
            }
        }

        .btn-primary:hover {
            background: #059669;
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }

        .logo-image {
            position: relative;
            z-index: 10;
            height: 16rem;
            width: 16rem;
            object-fit: contain;
            filter: drop-shadow(0 25px 25px rgba(0, 0, 0, 0.15));
            transition: transform 0.5s ease;
        }

        @media (min-width: 768px) {
            .logo-image {
                height: 20rem;
                width: 20rem;
            }
        }

        .logo-image:hover {
            transform: scale(1.05);
        }

        .header-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 2rem;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: flex-end;
        }

        .back-link {
            color: #000000ff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #10B981;
        }
    </style>
</head>

<body>

    <nav class="bg-white shadow-sm border-b border-gray-200 fixed w-full z-50 top-0 left-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('pnbp.landing') }}" class="text-2xl font-bold text-green-600">Pelaporan Hasil
                            Hutan</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('laporan.landing') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition text-gray-700 hover:text-green-700 hover:bg-green-50">Beranda</a>

                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                            @csrf
                            <button type="submit"
                                class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="landing-container" style="margin-top: 5rem;">
        <div class="landing-flex">
            <div class="landing-content">
                <div style="max-width: 42rem; margin: 0 auto;">
                    <h1 class="landing-title">
                        <span style="display: block;">Pelaporan</span>
                        <span class="landing-title-highlight">Hasil Hutan</span>
                    </h1>
                    <p class="landing-description">
                        Platform terintegrasi untuk pemantauan, pelaporan, dan analisis statistik kinerja industri
                        pengolahan kayu secara real-time.
                    </p>
                    <div class="landing-buttons">
                        <a href="{{ route('laporan.rekap') }}" class="btn-primary">
                            Lihat Data
                        </a>
                    </div>
                </div>
            </div>

            <div class="landing-logo">
                <img src="{{ asset('img/Logo Provinsi Jawa Tengah.png') }}" alt="Logo Jawa Tengah" class="logo-image">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="w-full border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-6 mt-auto">
        <div class="mx-auto max-w-6xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-col items-center md:items-start gap-1">
                <span class="text-sm font-bold text-green-600">Dinas Lingkungan Hidup & Kehutanan</span>
                <span class="text-xs text-gray-500">© {{ date('Y') }} DLHK Provinsi Jawa Tengah. All rights
                    reserved.</span>
            </div>
            <div class="flex items-center gap-3 text-xs font-medium text-gray-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sistem Berjalan Normal
            </div>
        </div>
    </footer>
</body>

</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Informasi Pemanfaatan &amp; Pengolahan Kayu Jawa Tengah</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#166534",
                        secondary: "#92400e",
                        "background-light": "#F9FAFB",
                        "background-dark": "#111827",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1F2937",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        xl: "1rem",
                        "2xl": "1.5rem",
                    },
                    backgroundImage: {
                        "forest-pattern": "url('https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')",
                    },
                },
            },
        };
    </script>

    <style>
        .custom-shape-divider-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }

        .custom-shape-divider-bottom svg {
            position: relative;
            display: block;
            width: calc(138% + 1.3px);
            height: 86px;
        }

        .custom-shape-divider-bottom .shape-fill {
            fill: #F9FAFB;
        }

        .dark .custom-shape-divider-bottom .shape-fill {
            fill: #111827;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 font-display transition-colors duration-300 min-h-screen flex flex-col">

    <nav class="absolute top-0 w-full z-20 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 flex items-center justify-center hover:scale-105 transition-transform overflow-hidden">
                    <img src="logo jateng.webp" alt="Logo Jawa Tengah" class="w-full h-full object-contain rounded-full">
                </div>
                <span class="text-white font-bold text-sm sm:text-base tracking-wide hidden sm:block drop-shadow-md">DLHK PROVINSI JAWA TENGAH</span>
            </div>
        </div>
    </nav>

    <header class="relative bg-primary min-h-[500px] flex items-center justify-center overflow-hidden pb-16">
        <div class="absolute inset-0 bg-forest-pattern bg-cover bg-center"></div>
        <div
            class="absolute inset-0 bg-gradient-to-b from-primary/90 via-primary/80 to-emerald-900/90 mix-blend-multiply">
        </div>
        <div class="absolute inset-0 bg-black/20"></div>

        <div class="absolute bottom-32 right-20 opacity-10 transform rotate-45">
            <svg fill="white" height="150" viewBox="0 0 24 24" width="150">
                <path
                    d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22l1-2.3A4.49,4.49 0 0,0 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z">
                </path>
            </svg>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto mt-12">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-snug drop-shadow-xl mb-8">
                SISTEM INFORMASI<br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-200 to-emerald-100">PEMANFAATAN &amp; PENGOLAHAN KAYU</span> JAWA TENGAH
            </h1>
        </div>

        <div class="custom-shape-divider-bottom">
            <svg data-name="Layer 1" preserveAspectRatio="none" viewBox="0 0 1200 120"
                xmlns="http://www.w3.org/2000/svg">
                <path class="shape-fill"
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z">
                </path>
            </svg>
        </div>
    </header>

    <main class="flex-grow relative z-10 -mt-28 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-5">

            <div
                class="group relative bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                <div class="h-1.5 bg-gradient-to-r from-green-500 to-emerald-700"></div>
                <div class="p-4 flex flex-col items-center text-center flex-grow">
                    <div class="w-12 h-12 mb-3 relative group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-green-100 dark:bg-green-900/30 rounded-full animate-pulse">
                        </div>
                        <div
                            class="relative w-full h-full rounded-full border-2 border-white dark:border-gray-700 shadow bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                            <span class="material-icons text-white text-2xl">forest</span>
                        </div>
                    </div>

                    <h2
                        class="text-lg font-extrabold text-gray-900 dark:text-white mb-1 group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors">
                        SIP-JATENG</h2>
                    <h3
                        class="text-[11px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2 border-b border-green-100 dark:border-green-800 pb-1">
                        Sistem Pengelolan PNBP Jateng</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-xs leading-snug line-clamp-3">Layanan digital untuk
                        memantau distribusi, sertifikasi, dan ketersediaan benih tani hutan secara real-time.</p>

                    <div class="mt-auto w-full">
                        <a class="inline-flex items-center justify-center w-full bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 font-semibold py-2 px-4 rounded-lg text-sm transition-all duration-200 border border-green-200 dark:border-green-800"
                            href="{{ route('pnbp.landing') }}" target="_blank">
                            <span>Akses Aplikasi</span>
                            <span
                                class="material-icons ml-1 text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="group relative bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                <div class="h-1.5 bg-gradient-to-r from-green-500 to-emerald-700"></div>
                <div class="p-4 flex flex-col items-center text-center flex-grow">
                    <div class="w-12 h-12 mb-3 relative group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-green-100 dark:bg-green-900/30 rounded-full animate-pulse">
                        </div>
                        <div
                            class="relative w-full h-full rounded-full border-2 border-white dark:border-gray-700 shadow bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                            <span class="material-icons text-white text-2xl">forest</span>
                        </div>
                    </div>

                    <h2
                        class="text-lg font-extrabold text-gray-900 dark:text-white mb-1 group-hover:text-green-700 dark:group-hover:text-green-400 transition-colors">
                        SIDI-HUT</h2>
                    <h3
                        class="text-[11px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wider mb-2 border-b border-green-100 dark:border-green-800 pb-1">
                        Database Industri Kehutanan</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-xs leading-snug line-clamp-3">Layanan digital untuk
                        menambahkan, memantau database keaktifan industri kehutanan di Jawa Tengah secara real-time.</p>

                    <div class="mt-auto w-full">
                        <a class="inline-flex items-center justify-center w-full bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 font-semibold py-2 px-4 rounded-lg text-sm transition-all duration-200 border border-green-200 dark:border-green-800"
                            href="{{ route('industri.dashboard') }}" target="_blank">
                            <span>Akses Aplikasi</span>
                            <span
                                class="material-icons ml-1 text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="group relative bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                <div class="h-1.5 bg-gradient-to-r from-amber-600 to-orange-800"></div>
                <div class="p-4 flex flex-col items-center text-center flex-grow">
                    <div class="w-12 h-12 mb-3 relative group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute inset-0 bg-orange-100 dark:bg-orange-900/30 rounded-full animate-pulse">
                        </div>
                        <div
                            class="relative w-full h-full rounded-full border-2 border-white dark:border-gray-700 shadow bg-gradient-to-br from-amber-600 to-orange-700 flex items-center justify-center">
                            <span class="material-icons text-white text-2xl">landscape</span>
                        </div>
                    </div>

                    <h2
                        class="text-lg font-extrabold text-gray-900 dark:text-white mb-1 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                        PELAPORAN PUHH</h2>
                    <h3
                        class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-2 border-b border-amber-100 dark:border-amber-800 pb-1">
                        Monitoring & Pelaporan Kehutanan</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-xs leading-snug line-clamp-3">Portal pelaporan kehutanan terpadu untuk pengumpulan, pengelolaan,
                        dan rekapitulasi data.</p>

                    <div class="mt-auto w-full">
                        <a class="inline-flex items-center justify-center w-full bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-semibold py-2 px-4 rounded-lg text-sm transition-all duration-200 border border-amber-200 dark:border-amber-800"
                        href="{{ route('laporan.index') }}" target="_blank">
                            <span>Akses Aplikasi</span>
                            <span
                                class="material-icons ml-1 text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-6 mt-auto">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <span class="material-icons text-primary text-xs">eco</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">© {{ date('Y') }} Dinas Lingkungan Hidup dan
                    Kehutanan.</p>
            </div>
        </div>
    </footer>

</body>

</html>
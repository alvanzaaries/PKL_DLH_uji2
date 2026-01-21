<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 - Halaman Tidak Ditemukan | DLHK Jawa Tengah</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
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
                        accent: "#059669",
                        "background-light": "#F9FAFB",
                        "background-dark": "#111827",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1F2937",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .organic-shape {
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            animation: morph 8s ease-in-out infinite;
        }

        @keyframes morph {

            0%,
            100% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            }

            50% {
                border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%;
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .leaf-fall {
            animation: leafFall 4s ease-in-out infinite;
        }

        @keyframes leafFall {

            0%,
            100% {
                transform: rotate(-5deg) translateY(0);
            }

            50% {
                transform: rotate(5deg) translateY(10px);
            }
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 font-display min-h-screen flex flex-col">

    <!-- Navigation -->
    <header
        class="sticky top-0 z-50 w-full border-b border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md px-6 py-4">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <div class="w-10 h-10 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah"
                        class="w-full h-full object-contain">
                </div>
                <span class="hidden md:block text-primary font-bold text-sm tracking-wide">DLHK PROVINSI JAWA
                    TENGAH</span>
            </a>
            <nav class="flex items-center gap-6">
                <a href="{{ url('/') }}"
                    class="hidden md:flex items-center gap-1 text-sm font-semibold text-gray-600 hover:text-primary transition-colors">
                    <span class="material-icons text-sm">home</span>
                    Beranda
                </a>
                <a href="{{ url('/') }}"
                    class="bg-primary hover:bg-primary/90 text-white text-sm font-bold px-5 py-2.5 rounded-lg shadow-lg shadow-primary/20 transition-all active:scale-95">
                    Portal Utama
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
        <div class="relative w-full max-w-4xl text-center">

            <!-- Background Decorative Element -->
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 md:w-96 md:h-96 bg-primary/5 dark:bg-primary/10 organic-shape -z-10 blur-2xl">
            </div>

            <!-- 404 Illustration -->
            <div class="relative inline-block mb-8 float-animation">
                <!-- Eco Icon -->
                <span
                    class="absolute -top-8 left-1/2 -translate-x-1/2 material-icons text-6xl text-primary/30 select-none">eco</span>

                <!-- Big 404 Number -->
                <h1
                    class="text-[100px] md:text-[160px] font-extrabold leading-none tracking-tighter text-primary/10 dark:text-primary/20 flex items-center justify-center select-none">
                    4
                    <span class="inline-flex items-center justify-center mx-2">
                        <div
                            class="w-20 h-20 md:w-28 md:h-28 rounded-full bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center shadow-2xl shadow-primary/30">
                            <span class="material-icons text-white text-4xl md:text-5xl">park</span>
                        </div>
                    </span>
                    4
                </h1>

                <!-- Underline -->
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2">
                    <div
                        class="h-1.5 w-32 mx-auto bg-gradient-to-r from-primary to-emerald-500 rounded-full opacity-60">
                    </div>
                </div>
            </div>

            <!-- Messaging -->
            <div class="space-y-4 max-w-xl mx-auto mt-8">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">
                    Oops! Halaman Tidak Ditemukan
                </h2>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    Sepertinya Anda tersesat di luar jalur. Halaman yang Anda cari tidak tersedia atau telah dipindahkan
                    ke lokasi lain.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}"
                    class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold px-8 py-4 rounded-xl shadow-xl shadow-primary/30 transition-all hover:-translate-y-1 hover:shadow-2xl">
                    <span class="material-icons">home</span>
                    Kembali ke Beranda
                </a>
                <button onclick="history.back()"
                    class="flex items-center gap-2 border-2 border-primary/20 hover:border-primary/40 dark:border-gray-700 dark:hover:border-primary/50 text-primary dark:text-emerald-400 font-bold px-8 py-4 rounded-xl transition-all hover:bg-primary/5">
                    <span class="material-icons">arrow_back</span>
                    Halaman Sebelumnya
                </button>
            </div>
        </div>

        <!-- Decorative Leaf -->
        <div class="mt-16 opacity-40 hover:opacity-70 transition-all duration-500 cursor-default leaf-fall">
            <div class="flex flex-col items-center">
                <span class="material-icons text-6xl text-primary">spa</span>
                <p class="text-xs font-bold tracking-widest uppercase mt-3 text-secondary">Tersesat di Alam</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-6">
        <div class="mx-auto max-w-6xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-col items-center md:items-start gap-1">
                <span class="text-sm font-bold text-primary">Dinas Lingkungan Hidup & Kehutanan</span>
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
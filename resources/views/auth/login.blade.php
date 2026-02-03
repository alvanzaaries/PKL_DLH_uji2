<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DLHK</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah" class="logo">
            <h1 class="login-title">Login</h1>
            <p class="login-subtitle">Dinas Lingkungan Hidup dan Kehutanan</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="admin@example.com"
                    value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input"
                    placeholder="Masukkan password" required autocomplete="current-password">
            </div>

            <div class="form-group">
                <div class="g-recaptcha" 
                     data-sitekey="{{ config('services.recaptcha.site_key') }}"
                     data-callback="onRecaptchaSuccess"
                     data-expired-callback="onRecaptchaExpired"></div>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>

    <!-- Load reCAPTCHA script at the end -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onRecaptchaSuccess(token) {
            console.log('reCAPTCHA verified successfully');
        }
        
        function onRecaptchaExpired() {
            console.log('reCAPTCHA expired');
            grecaptcha.reset();
        }
    </script>
</body>

</html>

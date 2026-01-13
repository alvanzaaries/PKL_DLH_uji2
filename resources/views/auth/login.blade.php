@extends('layouts.app')

@section('title', 'Login - SISUDAH')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="w-full max-w-md bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <h1 class="text-xl font-semibold text-gray-900">Login</h1>
        <p class="mt-1 text-sm text-gray-600">Masuk untuk melanjutkan.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 p-3 border border-red-200 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full border rounded-md px-3 py-2" autocomplete="email" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input name="password" type="password" class="mt-1 w-full border rounded-md px-3 py-2" autocomplete="current-password" required />
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300" />
                    <span class="ml-2">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 rounded-md">Masuk</button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-green-600">&larr; Kembali ke beranda</a>
        </div>
    </div>
</div>
@endsection

<x-guest-layout title="Masuk — RuangBaca">
    <x-slot:eyebrow>027 · Autentikasi</x-slot:eyebrow>
    <x-slot:heading>Masuk</x-slot:heading>
    <x-slot:subheading>Gunakan email anggota untuk mengakses pinjaman & e-book.</x-slot:subheading>

    <x-auth-session-status class="mb-4 text-sm text-forest" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <label class="block text-sm text-charcoal">
            Email
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mt-1 block w-full border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </label>

        <label class="block text-sm text-charcoal">
            Password
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-1 block w-full border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-charcoal-muted">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-navy/30 text-navy focus:ring-brass"
                >
                Ingat saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-navy underline decoration-brass/50 underline-offset-4 hover:decoration-brass">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="rb-btn-primary w-full">Masuk</button>

        <p class="pt-2 text-center text-sm text-charcoal-muted">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-navy underline decoration-brass underline-offset-4">Daftar</a>
        </p>
    </form>
</x-guest-layout>

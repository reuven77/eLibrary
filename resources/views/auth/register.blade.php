<x-guest-layout title="Daftar — RuangBaca">
    <x-slot:eyebrow>027 · Keanggotaan</x-slot:eyebrow>
    <x-slot:heading>Daftar</x-slot:heading>
    <x-slot:subheading>Buat akun anggota untuk mengajukan pinjaman buku fisik.</x-slot:subheading>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <label class="block text-sm text-charcoal">
            Nama
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="mt-1 block w-full border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </label>

        <label class="block text-sm text-charcoal">
            Email
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
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
                autocomplete="new-password"
                class="mt-1 block w-full border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </label>

        <label class="block text-sm text-charcoal">
            Konfirmasi password
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="mt-1 block w-full border-navy/20 bg-onionskin/40 focus:border-brass focus:ring-brass"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </label>

        <button type="submit" class="rb-btn-primary w-full">Buat akun</button>

        <p class="pt-2 text-center text-sm text-charcoal-muted">
            Sudah terdaftar?
            <a href="{{ route('login') }}" class="text-navy underline decoration-brass underline-offset-4">Masuk</a>
        </p>
    </form>
</x-guest-layout>

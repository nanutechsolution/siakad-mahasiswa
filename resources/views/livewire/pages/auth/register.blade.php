<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // PERBAIKAN: Isi username otomatis dengan email (karena di DB wajib isi/NOT NULL)
        // Nanti username ini akan diupdate jadi NIM saat mahasiswa diterima.
        $validated['username'] = $validated['email']; 
        
        // Opsional: Set role jadi 'camaba' atau 'guest' agar tidak langsung jadi 'student'
        $validated['role'] = 'camaba';

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="relative z-10">
    <!-- Header Form -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Buat Akun Baru 🚀</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Daftar untuk mengakses portal akademik.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" 
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="Nama sesuai KTP">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input wire:model="email" id="email" type="email" name="email" required autocomplete="username" 
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="nama@email.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password"
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="Minimal 8 karakter">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="Ulangi password">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" 
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-900/20 text-sm font-bold text-white bg-brand-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue transition-all transform hover:-translate-y-0.5">
                <span wire:loading.remove>DAFTAR SEKARANG</span>
                <span wire:loading>MEMPROSES...</span>
            </button>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Sudah punya akun? 
                <a href="{{ route('login') }}" wire:navigate class="text-brand-blue hover:text-blue-700 font-bold dark:text-brand-gold dark:hover:text-yellow-400">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</div>
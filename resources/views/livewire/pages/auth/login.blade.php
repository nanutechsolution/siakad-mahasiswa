<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // 1. Ambil data user yang barusan login
        $user = auth()->user();

        // 2. Tentukan tujuan berdasarkan role
        $tujuan = match ($user->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'student' => route('student.dashboard', absolute: false),
            'lecturer' => route('lecturer.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        // 3. Redirect ke dashboard masing-masing
        $this->redirect($tujuan, navigate: true);
    }
}; ?>

<div class="relative z-10">
    <!-- Header Form -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Selamat Datang Kembali! 👋</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Silakan masuk menggunakan akun akademik Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email /
                Username</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input wire:model="form.email" id="email" type="text" name="email" required autofocus
                    autocomplete="username"
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="Contoh: 24TI001">
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password"
                    class="block text-sm font-bold text-slate-700 dark:text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-brand-blue hover:text-blue-700 dark:text-brand-gold dark:hover:text-yellow-400"
                        href="{{ route('password.request') }}" wire:navigate>
                        Lupa Password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input wire:model="form.password" id="password" type="password" name="password" required
                    autocomplete="current-password"
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <input wire:model="form.remember" id="remember" type="checkbox"
                    class="rounded border-slate-300 text-brand-blue shadow-sm focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:focus:ring-offset-slate-900 transition-colors">
                <span
                    class="ms-2 text-sm text-slate-600 group-hover:text-slate-900 dark:text-slate-400 dark:group-hover:text-slate-200 transition-colors">Ingat
                    saya di perangkat ini</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-900/20 text-sm font-bold text-white bg-brand-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue transition-all transform hover:-translate-y-0.5">
                <span wire:loading.remove>MASUK SEKARANG</span>
                <span wire:loading>MEMPROSES...</span>
            </button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <p class="text-xs text-slate-400">
            Butuh bantuan? <a href="#"
                class="text-slate-600 font-bold hover:underline dark:text-slate-300">Hubungi BAAK</a>
        </p>
    </div>
</div>

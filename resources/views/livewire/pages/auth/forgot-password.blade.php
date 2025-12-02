<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="relative z-10">
    <!-- Header Form -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Lupa Password? 🔑</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
            Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input wire:model="email" id="email" type="email" name="email" required autofocus 
                    class="block w-full pl-10 pr-4 py-3 rounded-xl border-slate-300 bg-white text-slate-900 shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white dark:focus:ring-brand-gold sm:text-sm"
                    placeholder="Masukkan email terdaftar">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" 
                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-900/20 text-sm font-bold text-white bg-brand-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue transition-all transform hover:-translate-y-0.5">
                <span wire:loading.remove>KIRIM LINK RESET</span>
                <span wire:loading>MENGIRIM...</span>
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-slate-500 hover:text-brand-blue dark:text-slate-400 dark:hover:text-white transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Login
            </a>
        </div>
    </form>
</div>
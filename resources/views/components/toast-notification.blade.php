<div x-data="{
        show: false,
        message: '',
        type: 'success', // success, error, info
        title: 'Berhasil',
        
        init() {
            Livewire.on('notify', (event) => {
                // Handle jika event dikirim sebagai array atau object
                const data = Array.isArray(event) ? event[0] : event;
                this.showNotification(data.message, data.type || 'success');
            });

            // 2. Cek Session Flash (Untuk Redirect dari Controller)
            @if (session('success'))
                this.showNotification('{{ session('success') }}', 'success');
            @elseif (session('error'))
                this.showNotification('{{ session('error') }}', 'error');
            @endif
        },

        showNotification(msg, type) {
            this.message = msg;
            this.type = type;
            this.title = type === 'error' ? 'Terjadi Kesalahan!' : 'Berhasil!';
            this.show = true;
            setTimeout(() => { this.show = false }, 4000);
        }
    }"
    class="fixed inset-0 z-[9999] flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start">

    {{-- Posisi: Kanan Atas --}}
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
        
        <div x-show="show"
             x-cloak
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="max-w-sm w-full bg-white shadow-2xl rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border-l-4"
             :class="type === 'error' ? 'border-red-500' : 'border-green-500'">
            
            <div class="p-4">
                <div class="flex items-start">
                    
                    {{-- Ikon Dinamis --}}
                    <div class="flex-shrink-0">
                        <template x-if="type === 'success'">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </template>
                        <template x-if="type === 'error'">
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                        </template>
                    </div>

                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-bold text-gray-900" x-text="title"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
                    </div>

                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
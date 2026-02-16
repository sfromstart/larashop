{{-- 글로벌 토스트 알림 (Alpine.js) --}}
<div x-data="toastNotification()"
     x-on:toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-[100] space-y-3 pointer-events-none"
     style="max-width: 380px;">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="pointer-events-auto flex items-start space-x-3 p-4 rounded-xl shadow-lg border backdrop-blur-sm"
             :class="{
                'bg-green-50 border-green-200 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200 text-red-800': toast.type === 'error',
                'bg-blue-50 border-blue-200 text-blue-800': toast.type === 'info',
                'bg-yellow-50 border-yellow-200 text-yellow-800': toast.type === 'warning',
             }">

            {{-- 아이콘 --}}
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
            </div>

            {{-- 메시지 --}}
            <p class="flex-1 text-sm font-medium" x-text="toast.message"></p>

            {{-- 닫기 버튼 --}}
            <button @click="removeToast(toast.id)" class="flex-shrink-0 opacity-60 hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    </template>
</div>

@push('scripts')
<script>
    function toastNotification() {
        return {
            toasts: [],
            nextId: 0,

            init() {
                // 세션 플래시 메시지 처리
                @if(session('toast_message'))
                    this.addToast({
                        message: '{{ session('toast_message') }}',
                        type: '{{ session('toast_type', 'success') }}'
                    });
                @endif
            },

            addToast(detail) {
                const id = this.nextId++;
                const toast = {
                    id: id,
                    message: detail.message || detail[0]?.message || '',
                    type: detail.type || detail[0]?.type || 'success',
                    visible: true
                };

                this.toasts.push(toast);

                // 3초 후 자동 사라짐
                setTimeout(() => {
                    this.removeToast(id);
                }, 3000);
            },

            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }
    }
</script>
@endpush

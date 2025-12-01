@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="relative">
    <button type="button"
            class="relative p-2 text-zinc-400 hover:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg"
            onclick="window.location.href='{{ route('notifications') }}'">
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-medium bg-red-500 text-white rounded-full notification-badge">
                {{ $unreadCount }}
            </span>
        @endif
    </button>
</div>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 lenis">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
            <span class="text-lg font-semibold">WebDev Services</span>
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Main Menu')" class="grid">
                <flux:navlist.item icon="squares-2x2" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                @if (auth()->user()->role === 'admin')
                    <!-- Admin Menu -->
                    <flux:navlist.item icon="user-group" :href="route('admin.users')"
                        :current="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('Kelola User') }}
                    </flux:navlist.item>

                    <!-- PERBAIKAN: Gunakan icon shopping-cart yang tersedia -->
                    <flux:navlist.item icon="shopping-cart" :href="route('admin.orders')"
                        :current="request()->routeIs('admin.orders')" wire:navigate>
                        {{ __('Pesanan Masuk') }}
                    </flux:navlist.item>

                    <!-- PERBAIKAN: Gunakan icon chat-bubble-left-right yang tersedia -->
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('admin.feedbacks')"
                        :current="request()->routeIs('admin.feedbacks')" wire:navigate>
                        {{ __('Kritik & Saran') }}
                    </flux:navlist.item>
                @else
                    <!-- User Menu -->
                    <flux:navlist.item icon="shopping-cart" :href="route('user.orders')"
                        :current="request()->routeIs('user.orders')" wire:navigate>
                        {{ __('Pesanan Saya') }}
                    </flux:navlist.item>

                    <!-- PERBAIKAN: Gunakan icon clock yang tersedia -->
                    <flux:navlist.item icon="clock" :href="route('user.history')"
                        :current="request()->routeIs('user.history')" wire:navigate>
                        {{ __('Riwayat') }}
                    </flux:navlist.item>

                    <!-- PERBAIKAN: Gunakan icon chat-bubble-left-right yang tersedia -->
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('user.feedback')"
                        :current="request()->routeIs('user.feedback')" wire:navigate>
                        {{ __('Kritik & Saran') }}
                    </flux:navlist.item>
                @endif
            </flux:navlist.group>

            <!-- Common Menu for both roles -->
            <flux:navlist.group :heading="__('General')" class="grid">
                <flux:navlist.item icon="bell" :href="route('notifications')"
                    :current="request()->routeIs('notifications')" wire:navigate>
                    {{ __('Notifikasi') }}
                </flux:navlist.item>

                <flux:navlist.item icon="envelope" :href="route('messages')" :current="request()->routeIs('messages')"
                    wire:navigate>
                    {{ __('Pesan') }}
                </flux:navlist.item>

                <flux:navlist.item icon="question-mark-circle" :href="route('help')" wire:navigate>
                    {{ __('Bantuan') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevron-up-down" data-test="sidebar-menu-button" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                <span class="truncate text-xs capitalize text-blue-600 dark:text-blue-400">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                        {{ __('Pengaturan') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-end-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Keluar') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                <span class="truncate text-xs capitalize text-blue-600 dark:text-blue-400">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                        {{ __('Pengaturan') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-end-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Keluar') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <!-- Lenis -->
    <script src="https://unpkg.com/lenis@1.3.15/dist/lenis.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Initialize Lenis with Framer Motion integration
        document.addEventListener('DOMContentLoaded', function() {
            const lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
                smoothTouch: false,
                touchMultiplier: 2,
            });

            // Sync Lenis with Framer Motion
            lenis.on('scroll', ({
                scroll,
                limit,
                velocity,
                direction,
                progress
            }) => {
                // You can use these values with Framer Motion if needed
            });

            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }

            requestAnimationFrame(raf);
        });
    </script>

    @fluxScripts
</body>

</html>

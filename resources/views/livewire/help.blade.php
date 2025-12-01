<div>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-gray-900 dark:to-gray-800 py-responsive safe-top">
        <div class="responsive-container max-w-4xl mx-auto">

            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8 md:mb-12">
                <h1 class="text-responsive-xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4">
                    Pusat Bantuan
                </h1>
                <p class="text-responsive-base text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Temukan jawaban untuk pertanyaan umum dan panduan penggunaan platform kami
                </p>
            </div>

            <!-- FAQ Section -->
            <div class="card-responsive mb-6 sm:mb-8">
                <h2 class="text-responsive-md font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Pertanyaan Umum</h2>

                <div class="space-y-4 sm:space-y-6">
                    <!-- FAQ Item 1 -->
                    <div class="border-b border-gray-200 dark:border-gray-600 pb-4 sm:pb-6">
                        <h3 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                            Bagaimana cara memulai pesanan baru?
                        </h3>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-300">
                            Pergi ke halaman "Buat Pesanan Baru", pilih paket yang sesuai dengan kebutuhan Anda,
                            isi detail proyek, dan lanjutkan ke pembayaran.
                        </p>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border-b border-gray-200 dark:border-gray-600 pb-4 sm:pb-6">
                        <h3 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                            Apa perbedaan antara berbagai paket yang tersedia?
                        </h3>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-300">
                            Setiap paket dirancang untuk kebutuhan bisnis yang berbeda, mulai dari usaha kecil
                            hingga enterprise. Perbedaan utama terletak pada fitur, kapasitas, dan dukungan yang disediakan.
                        </p>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border-b border-gray-200 dark:border-gray-600 pb-4 sm:pb-6">
                        <h3 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                            Berapa lama waktu pengerjaan proyek?
                        </h3>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-300">
                            Waktu pengerjaan bervariasi tergantung kompleksitas proyek dan paket yang dipilih.
                            Rata-rata antara 7-30 hari kerja.
                        </p>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border-b border-gray-200 dark:border-gray-600 pb-4 sm:pb-6">
                        <h3 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                            Apakah tersedia revisi setelah proyek selesai?
                        </h3>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-300">
                            Ya, setiap paket termasuk sejumlah revisi gratis. Jumlah revisi tergantung pada
                            paket yang Anda pilih.
                        </p>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="pb-4 sm:pb-6">
                        <h3 class="text-responsive-sm font-semibold text-gray-900 dark:text-white mb-1 sm:mb-2">
                            Bagaimana cara menghubungi support?
                        </h3>
                        <p class="text-responsive-sm text-gray-600 dark:text-gray-300">
                            Anda dapat menghubungi tim support kami melalui live chat, telepon, atau email
                            yang tersedia di halaman kontak.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 sm:p-6 md:p-8">
                <div class="text-center">
                    <i data-lucide="help-circle" class="icon-responsive-lg text-blue-500 dark:text-blue-400 mx-auto mb-3 sm:mb-4"></i>
                    <h2 class="text-responsive-md font-bold text-blue-900 dark:text-blue-100 mb-3 sm:mb-4">Butuh Bantuan Lebih Lanjut?</h2>
                    <p class="text-responsive-sm text-blue-700 dark:text-blue-300 mb-4 sm:mb-6">
                        Tim support kami siap membantu Anda 24/7
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                        <a href="tel:+622112345678"
                           class="btn-responsive bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-200 inline-flex items-center justify-center gap-2 touch-target">
                            <i data-lucide="phone" class="icon-responsive-sm"></i>
                            <span class="hidden xs:inline">+62 21 1234 5678</span>
                            <span class="xs:hidden">Telepon</span>
                        </a>

                        <a href="mailto:support@example.com"
                           class="btn-responsive border border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white dark:hover:text-white transition-colors duration-200 inline-flex items-center justify-center gap-2 touch-target">
                            <i data-lucide="mail" class="icon-responsive-sm"></i>
                            <span class="hidden xs:inline">support@example.com</span>
                            <span class="xs:hidden">Email</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Bottom Navigation -->
            <div class="fixed-bottom-nav show-on-mobile mt-8">
                <div class="flex justify-around items-center py-3">
                    <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                        <i data-lucide="home" class="icon-responsive-md mb-1"></i>
                        <span class="text-xs">Dashboard</span>
                    </a>
                    <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                        <i data-lucide="package" class="icon-responsive-md mb-1"></i>
                        <span class="text-xs">Pesanan</span>
                    </a>
                    <a href="{{ route('landing-page') }}" class="flex flex-col items-center text-green-600 dark:text-green-400">
                        <div class="bg-green-600 text-white p-3 rounded-full -mt-6 shadow-lg">
                            <i data-lucide="plus" class="icon-responsive-md"></i>
                        </div>
                        <span class="text-xs mt-2">Pesan</span>
                    </a>
                    <a href="{{ route('user.history') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                        <i data-lucide="history" class="icon-responsive-md mb-1"></i>
                        <span class="text-xs">Riwayat</span>
                    </a>
                    <a href="{{ route('user.feedback') }}" class="flex flex-col items-center text-gray-600 dark:text-gray-400">
                        <i data-lucide="message-square" class="icon-responsive-md mb-1"></i>
                        <span class="text-xs">Feedback</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function() {
        // Initialize Lucide icons
        if (window.Lucide) {
            window.Lucide.createIcons();
        }

        // Handle mobile bottom nav active state
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.fixed-bottom-nav a');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (currentPath === href || currentPath.startsWith(href + '/')) {
                link.classList.remove('text-gray-600', 'dark:text-gray-400');
                link.classList.add('text-blue-600', 'dark:text-blue-400');
            }
        });

        // Add touch feedback for buttons
        document.querySelectorAll('.touch-target').forEach(button => {
            button.addEventListener('touchstart', function() {
                this.classList.add('opacity-75', 'scale-95');
            });

            button.addEventListener('touchend', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });

            button.addEventListener('touchcancel', function() {
                this.classList.remove('opacity-75', 'scale-95');
            });
        });

        // Handle FAQ accordion on mobile
        if (window.innerWidth < 768) {
            const faqItems = document.querySelectorAll('.border-b');
            faqItems.forEach(item => {
                const question = item.querySelector('h3');
                const answer = item.querySelector('p');

                if (question && answer) {
                    // Hide answer by default on mobile
                    answer.classList.add('hidden');

                    question.addEventListener('click', function() {
                        answer.classList.toggle('hidden');
                        const icon = this.querySelector('i');
                        if (icon) {
                            if (answer.classList.contains('hidden')) {
                                icon.classList.remove('rotate-90');
                            } else {
                                icon.classList.add('rotate-90');
                            }
                        }
                    });

                    // Add chevron icon
                    question.innerHTML += ' <i data-lucide="chevron-right" class="icon-responsive-xs inline ml-1 transition-transform"></i>';
                }
            });

            // Re-initialize icons
            if (window.Lucide) {
                window.Lucide.createIcons();
            }
        }
    });

    // Re-initialize icons on Livewire updates
    document.addEventListener('livewire:update', function() {
        if (window.Lucide) {
            window.Lucide.createIcons();
        }
    });

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            if (window.Lucide) {
                window.Lucide.createIcons();
            }
        }, 300);
    });
</script>

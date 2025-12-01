// resources/js/order-progress.js
document.addEventListener('DOMContentLoaded', function() {
    // Polling untuk update progress
    let isPolling = false;
    let pollingInterval;

    function startPolling(orderId) {
        if (isPolling) return;

        isPolling = true;
        pollingInterval = setInterval(() => {
            fetch(`/api/orders/${orderId}/progress`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateProgressUI(data);
                    }
                })
                .catch(error => {
                    console.error('Polling error:', error);
                    stopPolling();
                });
        }, 30000); // Poll setiap 30 detik
    }

    function stopPolling() {
        isPolling = false;
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    }

    function updateProgressUI(data) {
        // Update progress bar
        const progressBar = document.getElementById('overall-progress-bar');
        const progressText = document.getElementById('overall-progress-text');

        if (progressBar && progressText) {
            progressBar.style.width = `${data.overall_progress}%`;
            progressText.textContent = `${data.overall_progress}%`;
        }

        // Update status badges
        const statusBadge = document.getElementById('order-status-badge');
        const paymentBadge = document.getElementById('payment-status-badge');

        if (statusBadge && data.order.status_display_name) {
            statusBadge.textContent = data.order.status_display_name;
            statusBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${data.order.status_badge_color}-100 dark:bg-${data.order.status_badge_color}-900 text-${data.order.status_badge_color}-800 dark:text-${data.order.status_badge_color}-200`;
        }

        if (paymentBadge && data.order.payment_status_display_name) {
            paymentBadge.textContent = data.order.payment_status_display_name;
            paymentBadge.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-${data.order.payment_status_badge_color}-100 dark:bg-${data.order.payment_status_badge_color}-900 text-${data.order.payment_status_badge_color}-800 dark:text-${data.order.payment_status_badge_color}-200`;
        }

        // Update progress list
        if (data.progress_updates && data.progress_updates.length > 0) {
            updateProgressList(data.progress_updates);
        }
    }

    function updateProgressList(updates) {
        const progressList = document.getElementById('progress-updates-list');
        if (!progressList) return;

        // Clear existing updates except the first one (template)
        while (progressList.children.length > 1) {
            progressList.removeChild(progressList.lastChild);
        }

        // Add new updates
        updates.forEach(update => {
            const updateElement = createProgressUpdateElement(update);
            progressList.appendChild(updateElement);
        });
    }

    function createProgressUpdateElement(update) {
        const div = document.createElement('div');
        div.className = 'border-l-2 border-blue-500 pl-4 py-2';
        div.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-medium text-gray-900 dark:text-white">${update.title}</h4>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-${update.status_color}-100 dark:bg-${update.status_color}-900 text-${update.status_color}-800 dark:text-${update.status_color}-200">
                        ${update.status_label}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ${new Date(update.created_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}
                    </span>
                </div>
            </div>
            ${update.description ? `<p class="text-sm text-gray-600 dark:text-gray-300 mb-3">${update.description}</p>` : ''}
            <div class="flex items-center">
                <div class="flex-1 mr-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: ${update.percentage}%"></div>
                    </div>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${update.percentage}%</span>
            </div>
        `;
        return div;
    }

    // Start polling if on order detail page
    const orderId = document.body.dataset.orderId;
    if (orderId) {
        startPolling(orderId);

        // Stop polling when user leaves page
        window.addEventListener('beforeunload', stopPolling);
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPolling();
            } else {
                startPolling(orderId);
            }
        });
    }
});

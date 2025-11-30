<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Payment extends Component
{
    public $order;
    public $orderId;
    public $isProcessing = false;
    public $paymentUrl;
    public $errorMessage = null;

    public function mount($order)
    {
        $this->orderId = $order;
        $this->order = Order::with('package')
            ->where('id', $order)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Generate payment URL jika belum ada
        if (!$this->order->payment_url && $this->order->payment_status === Order::PAYMENT_PENDING) {
            $this->generatePaymentUrl();
        } else {
            $this->paymentUrl = $this->order->payment_url;
        }
    }

    public function generatePaymentUrl()
    {
        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            // Midtrans configuration
            $merchantId = config('services.midtrans.merchant_id');
            $serverKey = config('services.midtrans.server_key');
            $isProduction = config('services.midtrans.is_production', false);

            if (!$merchantId || !$serverKey) {
                throw new \Exception('Konfigurasi Midtrans belum lengkap. Silakan hubungi administrator.');
            }

            $baseUrl = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            // Prepare transaction details
            $transactionDetails = [
                'order_id' => $this->order->order_number,
                'gross_amount' => (int) $this->order->total_price,
            ];

            // Prepare customer details
            $customerDetails = [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? '081234567890',
            ];

            // Prepare item details
            $itemDetails = [
                [
                    'id' => $this->order->package_id,
                    'price' => (int) $this->order->base_price,
                    'quantity' => 1,
                    'name' => $this->order->package->name,
                    'brand' => 'WebDev Company',
                    'category' => 'Website Development',
                    'merchant_name' => config('app.name', 'Laravel'),
                ]
            ];

            // Add discount as item if exists
            if ($this->order->discount_amount > 0) {
                $itemDetails[] = [
                    'id' => 'discount',
                    'price' => - (int) $this->order->discount_amount,
                    'quantity' => 1,
                    'name' => 'Diskon ' . ($this->order->duration * 5) . '%',
                ];
            }

            // Prepare request payload
            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('payment.callback'),
                    'error' => route('payment.callback'),
                    'pending' => route('payment.callback'),
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'hours',
                    'duration' => 24,
                ],
                'credit_card' => [
                    'secure' => true,
                    'bank' => 'bni',
                ]
            ];

            Log::info('Midtrans Payload:', $payload);

            // Make request to Midtrans
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(30)
                ->retry(3, 100)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Laravel/' . app()->version(),
                ])
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['redirect_url'])) {
                    $this->paymentUrl = $data['redirect_url'];

                    // Update order with payment URL and merchant info
                    $this->order->update([
                        'payment_url' => $this->paymentUrl,
                        'midtrans_order_id' => $this->order->order_number,
                        'midtrans_merchant_id' => $merchantId,
                    ]);

                    $this->dispatch('payment-url-generated');
                    Log::info('Payment URL generated for order: ' . $this->order->order_number);
                } else {
                    throw new \Exception('URL redirect tidak ditemukan dalam respons Midtrans.');
                }
            } else {
                $errorResponse = $response->json();
                Log::error('Midtrans API Error Response:', $errorResponse);
                throw new \Exception('Midtrans API Error: ' . ($errorResponse['error_message'] ?? $response->body()));
            }

        } catch (\Exception $e) {
            Log::error('Payment URL Generation Error: ' . $e->getMessage());
            $this->errorMessage = 'Gagal menghasilkan URL pembayaran: ' . $e->getMessage();
            $this->dispatch('payment-error', ['message' => $this->errorMessage]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function proceedToPayment()
    {
        if ($this->paymentUrl) {
            // Log sebelum redirect
            Log::info('Redirecting to payment for order: ' . $this->order->order_number);
            return redirect()->away($this->paymentUrl);
        } else {
            $this->errorMessage = 'URL pembayaran tidak tersedia. Silakan refresh halaman.';
            $this->dispatch('payment-error', ['message' => $this->errorMessage]);
        }
    }

    public function refreshPayment()
    {
        $this->paymentUrl = null;
        $this->errorMessage = null;
        $this->generatePaymentUrl();
    }

    public function render()
    {
        return view('livewire.payment');
    }
}

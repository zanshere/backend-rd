<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Payment extends Component
{
    public $order;
    public $orderId;
    public $isProcessing = false;
    public $paymentUrl;

    public function mount($order)
    {
        $this->orderId = $order;
        $this->order = Order::with('package')
            ->where('id', $order)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Jika belum ada payment URL, generate ke Midtrans
        if (!$this->order->payment_url && $this->order->payment_status === Order::PAYMENT_PENDING) {
            $this->generatePaymentUrl();
        } else {
            $this->paymentUrl = $this->order->payment_url;
        }
    }

    public function generatePaymentUrl()
    {
        $this->isProcessing = true;

        try {
            // Midtrans API configuration
            $serverKey = config('services.midtrans.server_key');
            $isProduction = config('services.midtrans.is_production');

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
            ];

            // Prepare item details - main package
            $itemDetails = [
                [
                    'id' => $this->order->package_id,
                    'price' => (int) $this->order->package->base_price,
                    'quantity' => 1,
                    'name' => $this->order->package->name,
                ]
            ];

            // Add custom features as additional items if any
            $customFeatures = $this->order->custom_features ?? [];
            foreach ($customFeatures as $feature) {
                // Parse feature to see if it's an addon with price
                if (str_contains($feature, 'Durasi layanan')) {
                    // Duration is already included in base price calculation
                    continue;
                }

                // Add other features as items with nominal price
                $itemDetails[] = [
                    'id' => 'feature_' . uniqid(),
                    'price' => 1000, // Nominal price for features
                    'quantity' => 1,
                    'name' => $feature,
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
                ]
            ];

            // Make request to Midtrans
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $this->paymentUrl = $data['redirect_url'];

                // Update order with payment URL (you might want to add this field to your Order model)
                // For now, we'll store it in session or you can add a payment_url field to orders table
                Session::put('payment_url_' . $this->order->id, $this->paymentUrl);

                $this->dispatch('payment-url-generated');
            } else {
                throw new \Exception('Failed to generate payment URL: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->dispatch('payment-error', [
                'message' => 'Gagal menghasilkan URL pembayaran: ' . $e->getMessage()
            ]);
        } finally {
            $this->isProcessing = false;
        }
    }

    public function proceedToPayment()
    {
        if ($this->paymentUrl) {
            return redirect()->away($this->paymentUrl);
        }
    }

    public function checkPaymentStatus()
    {
        // You can implement payment status checking here
        // This would typically involve checking with Midtrans API
        $this->dispatch('checking-payment');
    }

    public function render()
    {
        return view('livewire.payment');
    }
}

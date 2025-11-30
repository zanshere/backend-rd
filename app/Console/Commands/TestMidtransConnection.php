<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestMidtransConnection extends Command
{
    protected $signature = 'midtrans:test';
    protected $description = 'Test connection to Midtrans API';

    public function handle()
    {
        $merchantId = config('services.midtrans.merchant_id');
        $serverKey = config('services.midtrans.server_key');
        $isProduction = config('services.midtrans.is_production');

        $this->info('Midtrans Configuration:');
        $this->line('Merchant ID: ' . $merchantId);
        $this->line('Server Key: ' . $serverKey);
        $this->line('Production: ' . ($isProduction ? 'Yes' : 'No'));

        if (!$merchantId || !$serverKey) {
            $this->error('Midtrans configuration is incomplete!');
            return 1;
        }

        $baseUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post($baseUrl, [
                    'transaction_details' => [
                        'order_id' => 'TEST-' . time(),
                        'gross_amount' => 10000,
                    ]
                ]);

            if ($response->successful()) {
                $this->info('✅ Midtrans connection successful!');
                $this->line('Response: ' . $response->body());
            } else {
                $this->error('❌ Midtrans connection failed!');
                $this->line('Status: ' . $response->status());
                $this->line('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Midtrans connection error: ' . $e->getMessage());
        }

        return 0;
    }
}

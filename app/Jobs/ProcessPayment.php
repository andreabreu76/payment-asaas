<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    /**
     * Get the payment associated with the job.
     *
     * @return \App\Models\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $asaasApiKey = config('app.asaas_api_key', env('ASAAS_API_KEY'));
        $asaasApiUrl = config('app.asaas_api_url', env('ASAAS_API_URL', 'https://sandbox.asaas.com/api/v3'));

        try {
            // Prepare payment data based on billing type
            $paymentData = $this->payment->payment_data;

            // Call Asaas API
            $response = Http::withHeaders([
                'access_token' => $asaasApiKey,
            ])->post($asaasApiUrl . '/payments', $paymentData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Update payment with success status and response data
                $this->payment->update([
                    'payment_id' => $responseData['id'] ?? null,
                    'status' => 'success',
                    'response_data' => $responseData,
                ]);

                Log::info('Payment processed successfully', [
                    'payment_id' => $this->payment->id,
                    'asaas_id' => $responseData['id'] ?? null
                ]);
            } else {
                // Update payment with failed status
                $this->payment->update([
                    'status' => 'failed',
                    'response_data' => [
                        'error' => $response->body(),
                        'status_code' => $response->status()
                    ],
                ]);

                Log::error('Payment processing failed', [
                    'payment_id' => $this->payment->id,
                    'error' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            // Update payment with failed status
            $this->payment->update([
                'status' => 'failed',
                'response_data' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ],
            ]);

            Log::error('Exception during payment processing', [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage()
            ]);

            // Re-throw the exception to trigger job retry if needed
            throw $e;
        }
    }
}

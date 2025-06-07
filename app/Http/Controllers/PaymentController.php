<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPayment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PaymentResource;

class PaymentController extends Controller
{
    protected $asaasApiKey;
    protected $asaasApiUrl;

    public function __construct()
    {
        $this->asaasApiKey = config('app.asaas_api_key', env('ASAAS_API_KEY'));
        $this->asaasApiUrl = config('app.asaas_api_url', env('ASAAS_API_URL', 'https://sandbox.asaas.com/api/v3'));
    }

    /**
     * Show the payment form
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('payments.index')->with('errors', session('errors') ?: new \Illuminate\Support\ViewErrorBag);
    }

    /**
     * Process the payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cpfCnpj' => 'required|string|min:11|max:14',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:BOLETO,CREDIT_CARD,PIX',
            'amount' => 'required|numeric|min:1',
            // Credit card specific validation
            'creditCard.holderName' => 'required_if:payment_method,CREDIT_CARD|string|max:255',
            'creditCard.number' => 'required_if:payment_method,CREDIT_CARD|string|max:16',
            'creditCard.expiryMonth' => 'required_if:payment_method,CREDIT_CARD|string|max:2',
            'creditCard.expiryYear' => 'required_if:payment_method,CREDIT_CARD|string|max:4',
            'creditCard.ccv' => 'required_if:payment_method,CREDIT_CARD|string|max:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create customer if not exists
            $customer = $this->createCustomer($request);

            // Prepare payment data based on the selected method
            $paymentData = $this->preparePaymentData($request, $customer['id']);

            // Create a payment record with pending status
            $payment = Payment::create([
                'amount' => $request->amount,
                'customer_id' => $customer['id'],
                'billing_type' => $request->payment_method,
                'status' => 'pending',
                'description' => 'Pagamento via ' . $this->getPaymentMethodName($request->payment_method),
                'payment_data' => $paymentData,
            ]);

            // Dispatch the job to process the payment asynchronously
            ProcessPayment::dispatch($payment);

            // Store payment information in session for the thank you page
            session(['payment' => [
                'id' => $payment->id,
                'status' => 'pending',
                'billingType' => $payment->billing_type,
                'value' => $payment->amount,
                'description' => $payment->description,
                'customer' => $customer['id'],
                'message' => 'Seu pagamento está sendo processado. Você receberá uma confirmação em breve.'
            ]]);

            return redirect()->route('payments.thank-you');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Prepare payment data based on the selected method
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $customerId
     * @return array
     */
    protected function preparePaymentData(Request $request, $customerId)
    {
        $baseData = [
            'customer' => $customerId,
            'billingType' => $request->payment_method,
            'value' => $request->amount,
            'dueDate' => now()->format('Y-m-d'),
            'description' => 'Pagamento via ' . $this->getPaymentMethodName($request->payment_method),
        ];

        // Add method-specific data
        switch ($request->payment_method) {
            case 'CREDIT_CARD':
                $baseData['creditCard'] = [
                    'holderName' => $request->input('creditCard.holderName'),
                    'number' => $request->input('creditCard.number'),
                    'expiryMonth' => $request->input('creditCard.expiryMonth'),
                    'expiryYear' => $request->input('creditCard.expiryYear'),
                    'ccv' => $request->input('creditCard.ccv'),
                ];
                $baseData['creditCardHolderInfo'] = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'cpfCnpj' => $request->cpfCnpj,
                    'postalCode' => $request->input('creditCard.postalCode', ''),
                    'addressNumber' => $request->input('creditCard.addressNumber', ''),
                    'phone' => $request->phone,
                ];
                break;

            case 'BOLETO':
                $baseData['dueDate'] = now()->addDays(3)->format('Y-m-d');
                break;
        }

        return $baseData;
    }

    /**
     * Get payment method name
     *
     * @param  string  $method
     * @return string
     */
    protected function getPaymentMethodName($method)
    {
        $methods = [
            'BOLETO' => 'Boleto',
            'CREDIT_CARD' => 'Cartão de Crédito',
            'PIX' => 'PIX',
        ];

        return $methods[$method] ?? $method;
    }

    /**
     * Show the thank you page
     *
     * @return \Illuminate\View\View
     */
    public function thankYou()
    {
        $payment = session('payment');

        if (!$payment) {
            return redirect()->route('payments.index');
        }

        return view('payments.thank-you', compact('payment'));
    }

    /**
     * Create a customer in Asaas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function createCustomer(Request $request)
    {
        $response = Http::withHeaders([
            'access_token' => $this->asaasApiKey,
        ])->post($this->asaasApiUrl . '/customers', [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'cpfCnpj' => $request->cpfCnpj,
            'notificationDisabled' => false,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro ao criar cliente: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Process Boleto payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $customerId
     * @return array
     */
    protected function processBoleto(Request $request, $customerId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->asaasApiKey,
        ])->post($this->asaasApiUrl . '/payments', [
            'customer' => $customerId,
            'billingType' => 'BOLETO',
            'value' => $request->amount,
            'dueDate' => now()->addDays(3)->format('Y-m-d'),
            'description' => 'Pagamento via Boleto',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro ao processar boleto: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Process Credit Card payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $customerId
     * @return array
     */
    protected function processCreditCard(Request $request, $customerId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->asaasApiKey,
        ])->post($this->asaasApiUrl . '/payments', [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'value' => $request->amount,
            'dueDate' => now()->format('Y-m-d'),
            'description' => 'Pagamento via Cartão de Crédito',
            'creditCard' => [
                'holderName' => $request->input('creditCard.holderName'),
                'number' => $request->input('creditCard.number'),
                'expiryMonth' => $request->input('creditCard.expiryMonth'),
                'expiryYear' => $request->input('creditCard.expiryYear'),
                'ccv' => $request->input('creditCard.ccv'),
            ],
            'creditCardHolderInfo' => [
                'name' => $request->name,
                'email' => $request->email,
                'cpfCnpj' => $request->cpfCnpj,
                'postalCode' => $request->input('creditCard.postalCode', ''),
                'addressNumber' => $request->input('creditCard.addressNumber', ''),
                'phone' => $request->phone,
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro ao processar cartão de crédito: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Process Pix payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $customerId
     * @return array
     */
    protected function processPix(Request $request, $customerId)
    {
        $response = Http::withHeaders([
            'access_token' => $this->asaasApiKey,
        ])->post($this->asaasApiUrl . '/payments', [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'value' => $request->amount,
            'dueDate' => now()->format('Y-m-d'),
            'description' => 'Pagamento via PIX',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro ao processar PIX: ' . $response->body());
        }

        return $response->json();
    }
}

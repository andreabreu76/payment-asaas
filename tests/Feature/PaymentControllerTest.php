<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessPayment;
use App\Models\Payment;
use App\Models\User;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set the ASAAS_API_URL and ASAAS_API_KEY for testing
        config(['app.asaas_api_url' => 'https://api.asaas.com/v3']);
        config(['app.asaas_api_key' => env('ASAAS_API_KEY', 'test_api_key')]);

        // Disable middleware for testing
        $this->withoutMiddleware();
    }

    /**
     * Test that the payment form is displayed.
     */
    public function test_payment_form_is_displayed(): void
    {
        $response = $this->get('/payments');

        $response->assertStatus(200);
        $response->assertViewIs('payments.index');
    }

    /**
     * Test that a payment can be processed with boleto.
     */
    public function test_payment_can_be_processed_with_boleto(): void
    {
        // Mock the queue so jobs are not actually dispatched
        Queue::fake();

        // Mock the HTTP responses for customer API call
        Http::fake([
            '*customers*' => Http::response(['id' => 'cus_123456'], 200),
        ]);

        // Submit a payment form with boleto
        $response = $this->post('/payments', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpfCnpj' => '12345678901',
            'phone' => '1234567890',
            'payment_method' => 'BOLETO',
            'amount' => 100.00,
        ]);

        // Assert the user is redirected to the thank you page
        $response->assertRedirect('/payments/thank-you');

        // Assert the customer API was called with the correct data
        Http::assertSent(function ($request) {
            return $request->url() == config('app.asaas_api_url') . '/customers' &&
                   $request->data()['name'] == 'Test User';
        });

        // Assert a payment record was created in the database
        $this->assertDatabaseHas('payments', [
            'customer_id' => 'cus_123456',
            'billing_type' => 'BOLETO',
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        // Get the payment that was just created
        $payment = Payment::where('customer_id', 'cus_123456')
                          ->where('billing_type', 'BOLETO')
                          ->first();

        // Assert the ProcessPayment job was dispatched with the payment
        Queue::assertPushed(ProcessPayment::class, function ($job) use ($payment) {
            return $job->getPayment()->id === $payment->id;
        });
    }

    /**
     * Test that a payment can be processed with credit card.
     */
    public function test_payment_can_be_processed_with_credit_card(): void
    {
        // Mock the queue so jobs are not actually dispatched
        Queue::fake();

        // Mock the HTTP responses for customer API call
        Http::fake([
            '*customers*' => Http::response(['id' => 'cus_123456'], 200),
        ]);

        // Submit a payment form with credit card
        $response = $this->post('/payments', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpfCnpj' => '12345678901',
            'phone' => '1234567890',
            'payment_method' => 'CREDIT_CARD',
            'amount' => 100.00,
            'creditCard' => [
                'holderName' => 'TEST USER',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2030',
                'ccv' => '123',
                'postalCode' => '12345678',
                'addressNumber' => '123',
            ],
        ]);

        // Assert the user is redirected to the thank you page
        $response->assertRedirect('/payments/thank-you');

        // Assert the customer API was called with the correct data
        Http::assertSent(function ($request) {
            return $request->url() == config('app.asaas_api_url') . '/customers' &&
                   $request->data()['name'] == 'Test User';
        });

        // Assert a payment record was created in the database
        $this->assertDatabaseHas('payments', [
            'customer_id' => 'cus_123456',
            'billing_type' => 'CREDIT_CARD',
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        // Get the payment that was just created
        $payment = Payment::where('customer_id', 'cus_123456')
                          ->where('billing_type', 'CREDIT_CARD')
                          ->first();

        // Assert the ProcessPayment job was dispatched with the payment
        Queue::assertPushed(ProcessPayment::class, function ($job) use ($payment) {
            return $job->getPayment()->id === $payment->id;
        });
    }

    /**
     * Test that a payment can be processed with PIX.
     */
    public function test_payment_can_be_processed_with_pix(): void
    {
        // Mock the queue so jobs are not actually dispatched
        Queue::fake();

        // Mock the HTTP responses for customer API call
        Http::fake([
            '*customers*' => Http::response(['id' => 'cus_123456'], 200),
        ]);

        // Submit a payment form with PIX
        $response = $this->post('/payments', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpfCnpj' => '12345678901',
            'phone' => '1234567890',
            'payment_method' => 'PIX',
            'amount' => 100.00,
        ]);

        // Assert the user is redirected to the thank you page
        $response->assertRedirect('/payments/thank-you');

        // Assert the customer API was called with the correct data
        Http::assertSent(function ($request) {
            return $request->url() == config('app.asaas_api_url') . '/customers' &&
                   $request->data()['name'] == 'Test User';
        });

        // Assert a payment record was created in the database
        $this->assertDatabaseHas('payments', [
            'customer_id' => 'cus_123456',
            'billing_type' => 'PIX',
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        // Get the payment that was just created
        $payment = Payment::where('customer_id', 'cus_123456')
                          ->where('billing_type', 'PIX')
                          ->first();

        // Assert the ProcessPayment job was dispatched with the payment
        Queue::assertPushed(ProcessPayment::class, function ($job) use ($payment) {
            return $job->getPayment()->id === $payment->id;
        });
    }

    /**
     * Test that validation errors are returned for invalid input.
     */
    public function test_validation_errors_are_returned_for_invalid_input(): void
    {
        // Submit an invalid payment form (missing required fields)
        $response = $this->post('/payments', [
            'name' => '',
            'email' => 'not-an-email',
            'cpfCnpj' => '123', // too short
            'phone' => '',
            'payment_method' => 'INVALID_METHOD',
            'amount' => 0, // too small
        ]);

        // Assert validation errors are returned
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'cpfCnpj', 'phone', 'payment_method', 'amount']);
    }

    /**
     * Test that the thank you page is displayed with payment data.
     */
    public function test_thank_you_page_is_displayed_with_payment_data(): void
    {
        // Create a mock payment for the session (this is what our controller stores in session now)
        $payment = [
            'id' => 1,
            'status' => 'pending',
            'billingType' => 'BOLETO',
            'value' => 100.00,
            'description' => 'Pagamento via Boleto',
            'customer' => 'cus_123456',
            'message' => 'Seu pagamento está sendo processado. Você receberá uma confirmação em breve.'
        ];

        // Store the payment in the session
        session(['payment' => $payment]);

        // Visit the thank you page
        $response = $this->get('/payments/thank-you');

        // Assert the page is displayed with the payment data
        $response->assertStatus(200);
        $response->assertViewIs('payments.thank-you');
        $response->assertViewHas('payment', $payment);
    }

    /**
     * Test that the thank you page redirects to index if no payment in session.
     */
    public function test_thank_you_page_redirects_if_no_payment_in_session(): void
    {
        // Clear the session
        session()->forget('payment');

        // Visit the thank you page
        $response = $this->get('/payments/thank-you');

        // Assert redirect to payment form
        $response->assertRedirect('/payments');
    }
}

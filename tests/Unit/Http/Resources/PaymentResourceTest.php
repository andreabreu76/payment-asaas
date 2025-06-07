<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PaymentResource;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentResourceTest extends TestCase
{
    use WithFaker;

    /**
     * Test that the PaymentResource correctly transforms a payment model.
     */
    public function test_payment_resource_transformation(): void
    {
        // Create a mock payment object with the necessary properties
        $payment = (object) [
            'id' => 'pay_123456789',
            'customer' => 'cus_123456789',
            'value' => 100.00,
            'netValue' => 95.00,
            'billingType' => 'BOLETO',
            'status' => 'PENDING',
            'dueDate' => '2023-12-31',
            'description' => 'Test payment',
            'invoiceUrl' => 'https://example.com/invoice',
            'bankSlipUrl' => 'https://example.com/bankslip',
            'dateCreated' => '2023-01-01',
            'lastUpdateDate' => '2023-01-02',
        ];

        // Transform the payment using the resource
        $resource = new PaymentResource($payment);
        $result = $resource->toArray(request());

        // Assert that the transformation is correct
        $this->assertEquals($payment->id, $result['id']);
        $this->assertEquals($payment->customer, $result['customer']);
        $this->assertEquals($payment->value, $result['value']);
        $this->assertEquals($payment->netValue, $result['netValue']);
        $this->assertEquals($payment->billingType, $result['billingType']);
        $this->assertEquals($payment->status, $result['status']);
        $this->assertEquals($payment->dueDate, $result['dueDate']);
        $this->assertEquals($payment->description, $result['description']);
        $this->assertEquals($payment->invoiceUrl, $result['invoiceUrl']);
        $this->assertEquals($payment->bankSlipUrl, $result['bankSlipUrl']);
        $this->assertEquals($payment->dateCreated, $result['created_at']);
        $this->assertEquals($payment->lastUpdateDate, $result['updated_at']);
    }

    /**
     * Test that the PaymentResource correctly handles credit card payments.
     */
    public function test_payment_resource_with_credit_card(): void
    {
        // Create a mock payment object with credit card details
        $payment = (object) [
            'id' => 'pay_123456789',
            'customer' => 'cus_123456789',
            'value' => 100.00,
            'netValue' => 95.00,
            'billingType' => 'CREDIT_CARD',
            'status' => 'CONFIRMED',
            'dueDate' => '2023-12-31',
            'description' => 'Test payment with credit card',
            'invoiceUrl' => 'https://example.com/invoice',
            'creditCardBrand' => 'VISA',
            'creditCardNumber' => '**** **** **** 1234',
            'dateCreated' => '2023-01-01',
            'lastUpdateDate' => '2023-01-02',
        ];

        // Transform the payment using the resource
        $resource = new PaymentResource($payment);
        $result = $resource->toArray(request());

        // Assert that the transformation is correct
        $this->assertEquals($payment->id, $result['id']);
        $this->assertEquals($payment->billingType, $result['billingType']);
        $this->assertEquals($payment->creditCardBrand, $result['creditCard']['creditCardBrand']);
        $this->assertEquals($payment->creditCardNumber, $result['creditCard']['creditCardNumber']);
    }

    /**
     * Test that the PaymentResource correctly handles PIX payments.
     */
    public function test_payment_resource_with_pix(): void
    {
        // Create a mock payment object with PIX details
        $payment = (object) [
            'id' => 'pay_123456789',
            'customer' => 'cus_123456789',
            'value' => 100.00,
            'netValue' => 95.00,
            'billingType' => 'PIX',
            'status' => 'PENDING',
            'dueDate' => '2023-12-31',
            'description' => 'Test payment with PIX',
            'invoiceUrl' => 'https://example.com/invoice',
            'pixQrCodeUrl' => 'https://example.com/pix-qrcode',
            'pixCopiaECola' => 'pix-copy-paste-code',
            'dateCreated' => '2023-01-01',
            'lastUpdateDate' => '2023-01-02',
        ];

        // Transform the payment using the resource
        $resource = new PaymentResource($payment);
        $result = $resource->toArray(request());

        // Assert that the transformation is correct
        $this->assertEquals($payment->id, $result['id']);
        $this->assertEquals($payment->billingType, $result['billingType']);
        $this->assertEquals($payment->pixQrCodeUrl, $result['pixQrCodeUrl']);
        $this->assertEquals($payment->pixCopiaECola, $result['pixCopiaECola']);
    }

    /**
     * Test that the PaymentResource correctly handles null values.
     */
    public function test_payment_resource_with_null_values(): void
    {
        // Create a mock payment object with some null values
        $payment = (object) [
            'id' => 'pay_123456789',
            'customer' => 'cus_123456789',
            'value' => 100.00,
            'netValue' => 95.00,
            'billingType' => 'BOLETO',
            'status' => 'PENDING',
            'dueDate' => '2023-12-31',
            'description' => 'Test payment with nulls',
            'invoiceUrl' => 'https://example.com/invoice',
            'bankSlipUrl' => null,
            'pixQrCodeUrl' => null,
            'pixCopiaECola' => null,
            'dateCreated' => '2023-01-01',
            'lastUpdateDate' => '2023-01-02',
        ];

        // Transform the payment using the resource
        $resource = new PaymentResource($payment);
        $result = $resource->toArray(request());

        // Assert that the transformation is correct
        $this->assertEquals($payment->id, $result['id']);
        $this->assertNull($result['bankSlipUrl']);
        $this->assertNull($result['pixQrCodeUrl']);
        $this->assertNull($result['pixCopiaECola']);
    }
}

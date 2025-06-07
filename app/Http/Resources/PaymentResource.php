<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'value' => $this->value,
            'netValue' => $this->netValue,
            'billingType' => $this->billingType,
            'status' => $this->status,
            'dueDate' => $this->dueDate,
            'description' => $this->description,
            'invoiceUrl' => $this->invoiceUrl,
            'bankSlipUrl' => $this->bankSlipUrl ?? null,
            'pixQrCodeUrl' => $this->pixQrCodeUrl ?? null,
            'pixCopiaECola' => $this->pixCopiaECola ?? null,
            'creditCard' => $this->when($this->billingType === 'CREDIT_CARD', function () {
                return [
                    'creditCardBrand' => $this->creditCardBrand,
                    'creditCardNumber' => $this->creditCardNumber,
                ];
            }),
            'created_at' => $this->dateCreated,
            'updated_at' => $this->lastUpdateDate,
        ];
    }
}

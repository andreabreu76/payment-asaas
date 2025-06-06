@extends('layouts.app')

@section('title', 'Pagamento')

@section('content')
<div class="payment-form">
    <h2 class="mb-4">Formulário de Pagamento</h2>

    <form action="{{ route('payments.process') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="cpfCnpj" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control @error('cpfCnpj') is-invalid @enderror" id="cpfCnpj" name="cpfCnpj" value="{{ old('cpfCnpj') }}" required>
                    @error('cpfCnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Valor (R$)</label>
            <input type="number" step="0.01" min="1" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', '100.00') }}" required>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="payment-method">
            <h3 class="mb-3">Método de Pagamento</h3>

            <div class="payment-method-option @if(old('payment_method') == 'BOLETO') active @endif" data-method="BOLETO">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="boleto" value="BOLETO" @if(old('payment_method') == 'BOLETO') checked @endif required>
                    <label class="form-check-label" for="boleto">
                        <strong>Boleto Bancário</strong>
                    </label>
                </div>
                <p class="text-muted small mt-2">Pague via boleto bancário. O processamento pode levar até 3 dias úteis.</p>
            </div>

            <div class="payment-method-option @if(old('payment_method') == 'CREDIT_CARD') active @endif" data-method="CREDIT_CARD">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="CREDIT_CARD" @if(old('payment_method') == 'CREDIT_CARD') checked @endif required>
                    <label class="form-check-label" for="credit_card">
                        <strong>Cartão de Crédito</strong>
                    </label>
                </div>
                <p class="text-muted small mt-2">Pague com cartão de crédito. Aprovação imediata.</p>

                <div class="credit-card-details" style="display: @if(old('payment_method') == 'CREDIT_CARD') block @else none @endif;">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="creditCard_holderName" class="form-label">Nome no Cartão</label>
                                <input type="text" class="form-control @error('creditCard.holderName') is-invalid @enderror" id="creditCard_holderName" name="creditCard[holderName]" value="{{ old('creditCard.holderName') }}">
                                @error('creditCard.holderName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="creditCard_number" class="form-label">Número do Cartão</label>
                                <input type="text" class="form-control @error('creditCard.number') is-invalid @enderror" id="creditCard_number" name="creditCard[number]" value="{{ old('creditCard.number') }}">
                                @error('creditCard.number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="creditCard_expiryMonth" class="form-label">Mês de Expiração</label>
                                <input type="text" class="form-control @error('creditCard.expiryMonth') is-invalid @enderror" id="creditCard_expiryMonth" name="creditCard[expiryMonth]" placeholder="MM" value="{{ old('creditCard.expiryMonth') }}">
                                @error('creditCard.expiryMonth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="creditCard_expiryYear" class="form-label">Ano de Expiração</label>
                                <input type="text" class="form-control @error('creditCard.expiryYear') is-invalid @enderror" id="creditCard_expiryYear" name="creditCard[expiryYear]" placeholder="AAAA" value="{{ old('creditCard.expiryYear') }}">
                                @error('creditCard.expiryYear')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="creditCard_ccv" class="form-label">Código de Segurança (CVV)</label>
                                <input type="text" class="form-control @error('creditCard.ccv') is-invalid @enderror" id="creditCard_ccv" name="creditCard[ccv]" value="{{ old('creditCard.ccv') }}">
                                @error('creditCard.ccv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="creditCard_postalCode" class="form-label">CEP</label>
                                <input type="text" class="form-control @error('creditCard.postalCode') is-invalid @enderror" id="creditCard_postalCode" name="creditCard[postalCode]" value="{{ old('creditCard.postalCode') }}">
                                @error('creditCard.postalCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="creditCard_addressNumber" class="form-label">Número</label>
                                <input type="text" class="form-control @error('creditCard.addressNumber') is-invalid @enderror" id="creditCard_addressNumber" name="creditCard[addressNumber]" value="{{ old('creditCard.addressNumber') }}">
                                @error('creditCard.addressNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-method-option @if(old('payment_method') == 'PIX') active @endif" data-method="PIX">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="pix" value="PIX" @if(old('payment_method') == 'PIX') checked @endif required>
                    <label class="form-check-label" for="pix">
                        <strong>PIX</strong>
                    </label>
                </div>
                <p class="text-muted small mt-2">Pague via PIX. Aprovação imediata.</p>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Finalizar Pagamento</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle payment method selection
        const paymentOptions = document.querySelectorAll('.payment-method-option');
        const creditCardDetails = document.querySelector('.credit-card-details');

        paymentOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                paymentOptions.forEach(opt => opt.classList.remove('active'));

                // Add active class to clicked option
                this.classList.add('active');

                // Select the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;

                // Show/hide credit card details
                if (radio.value === 'CREDIT_CARD') {
                    creditCardDetails.style.display = 'block';
                } else {
                    creditCardDetails.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection

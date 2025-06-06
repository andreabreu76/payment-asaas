@extends('layouts.app')

@section('title', 'Pagamento Concluído')

@section('content')
<div class="thank-you-page">
    <div class="alert alert-success mb-4">
        <h2 class="mb-0">Obrigado pelo seu pagamento!</h2>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>Detalhes do Pagamento</h3>
        </div>
        <div class="card-body">
            <p><strong>ID do Pagamento:</strong> {{ $payment['id'] }}</p>
            <p><strong>Valor:</strong> R$ {{ number_format($payment['value'], 2, ',', '.') }}</p>
            <p><strong>Data de Vencimento:</strong> {{ \Carbon\Carbon::parse($payment['dueDate'])->format('d/m/Y') }}</p>
            <p><strong>Status:</strong>
                @if($payment['status'] == 'PENDING')
                    <span class="badge bg-warning">Pendente</span>
                @elseif($payment['status'] == 'CONFIRMED' || $payment['status'] == 'RECEIVED')
                    <span class="badge bg-success">Confirmado</span>
                @elseif($payment['status'] == 'OVERDUE')
                    <span class="badge bg-danger">Vencido</span>
                @elseif($payment['status'] == 'REFUNDED')
                    <span class="badge bg-info">Reembolsado</span>
                @elseif($payment['status'] == 'RECEIVED_IN_CASH')
                    <span class="badge bg-success">Recebido em Dinheiro</span>
                @else
                    <span class="badge bg-secondary">{{ $payment['status'] }}</span>
                @endif
            </p>
            <p><strong>Método de Pagamento:</strong>
                @if($payment['billingType'] == 'BOLETO')
                    Boleto Bancário
                @elseif($payment['billingType'] == 'CREDIT_CARD')
                    Cartão de Crédito
                @elseif($payment['billingType'] == 'PIX')
                    PIX
                @else
                    {{ $payment['billingType'] }}
                @endif
            </p>
        </div>
    </div>

    @if($payment['billingType'] == 'BOLETO' && isset($payment['bankSlipUrl']))
    <div class="card mb-4">
        <div class="card-header">
            <h3>Boleto Bancário</h3>
        </div>
        <div class="card-body text-center">
            <p>Clique no botão abaixo para visualizar e imprimir o boleto:</p>
            <a href="{{ $payment['bankSlipUrl'] }}" target="_blank" class="btn btn-primary">Visualizar Boleto</a>
        </div>
    </div>
    @endif

    @if($payment['billingType'] == 'PIX')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Pagamento via PIX</h3>
        </div>
        <div class="card-body text-center">
            @if(isset($payment['pixQrCodeUrl']))
            <div class="qr-code-container">
                <p><strong>Escaneie o QR Code abaixo:</strong></p>
                <img src="{{ $payment['pixQrCodeUrl'] }}" alt="QR Code PIX" class="img-fluid">
            </div>
            @endif

            @if(isset($payment['pixCopiaECola']))
            <div class="copy-paste-container mt-4">
                <p><strong>Ou copie o código PIX:</strong></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="pixCode" value="{{ $payment['pixCopiaECola'] }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyPixCode()">Copiar</button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($payment['billingType'] == 'CREDIT_CARD')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Pagamento com Cartão de Crédito</h3>
        </div>
        <div class="card-body text-center">
            <p>Seu pagamento com cartão de crédito foi processado.</p>
            @if(isset($payment['creditCard']))
            <p><strong>Bandeira:</strong> {{ $payment['creditCard']['creditCardBrand'] }}</p>
            <p><strong>Últimos dígitos:</strong> {{ $payment['creditCard']['creditCardNumber'] }}</p>
            @endif
        </div>
    </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('payments.index') }}" class="btn btn-outline-primary">Realizar Novo Pagamento</a>
    </div>
</div>
@endsection

@section('scripts')
@if($payment['billingType'] == 'PIX' && isset($payment['pixCopiaECola']))
<script>
    function copyPixCode() {
        const pixCodeInput = document.getElementById('pixCode');
        pixCodeInput.select();
        document.execCommand('copy');

        // Show feedback
        const button = document.querySelector('.copy-paste-container button');
        const originalText = button.textContent;
        button.textContent = 'Copiado!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }
</script>
@endif
@endsection

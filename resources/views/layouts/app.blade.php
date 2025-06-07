<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pagamentos - @yield('title', 'Home')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .payment-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .payment-method {
            margin-bottom: 20px;
        }
        .payment-method-option {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .payment-method-option.active {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .payment-details {
            margin-top: 20px;
        }
        .thank-you-page {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .qr-code-container {
            margin: 20px auto;
            max-width: 300px;
        }
        .copy-paste-container {
            margin: 20px auto;
            max-width: 500px;
            word-break: break-all;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Sistema de Pagamentos</h1>
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
                @endauth
            </div>
        </header>

        <main>
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="mt-5 text-center text-muted">
            <p>&copy; {{ date('Y') }} Sistema de Pagamentos. Todos os direitos reservados.</p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    @yield('scripts')
</body>
</html>

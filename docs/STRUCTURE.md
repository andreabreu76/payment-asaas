<!--
@ia-instructions: update-on-read
description: Este arquivo deve ser revisado por IA sempre que lido.
goals:
  - Corrigir ortografia e gramática
  - Melhorar explicações técnicas
  - Garantir exemplos de código atualizados
rules:
  - Preserve formatação Markdown
  - Não apague seções existentes sem permissão explícita
-->
# Estrutura do Projeto

Este documento contém informações detalhadas sobre a estrutura do código do projeto.

## Árvore de Diretórios

```
payment-asaas-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php     # Controlador de autenticação
│   │   │   └── PaymentController.php  # Controlador principal para processamento de pagamentos
│   │   └── Resources/
│   │       └── PaymentResource.php    # Recurso para padronização das respostas da API
│   └── Models/
│       └── User.php                   # Modelo de usuário com suporte a JWT
├── database/
│   └── seeders/
│       ├── AdminUserSeeder.php        # Seeder para criar o usuário admin
│       └── DatabaseSeeder.php         # Seeder principal
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php        # Página de login
│       ├── layouts/
│       │   └── app.blade.php          # Template principal do layout da aplicação
│       └── payments/
│           ├── index.blade.php        # Página com formulário de pagamento
│           └── thank-you.blade.php    # Página de agradecimento após o pagamento
└── routes/
    └── web.php                        # Definição das rotas da aplicação
```

## Descrição dos Arquivos Principais

### Autenticação

- `app/Models/User.php`: Modelo de usuário com suporte a JWT
  - Implementa a interface JWTSubject
  - Métodos para obter o identificador JWT e claims personalizados

- `app/Http/Controllers/AuthController.php`: Controlador responsável pela autenticação
  - `showLoginForm()`: Exibe o formulário de login
  - `login()`: Processa a tentativa de login e gera o token JWT
  - `logout()`: Encerra a sessão do usuário

- `resources/views/auth/login.blade.php`: Página de login
  - Formulário para autenticação com email e senha
  - Exibe credenciais padrão para facilitar o acesso

- `database/seeders/AdminUserSeeder.php`: Seeder para criar o usuário admin
  - Cria um usuário com credenciais admin@example.com:admin

### Processamento de Pagamentos

- `app/Http/Controllers/PaymentController.php`: Controlador responsável pelo processamento de pagamentos
  - `index()`: Exibe o formulário de pagamento
  - `process()`: Processa o pagamento com base no método selecionado
  - `thankYou()`: Exibe a página de agradecimento com detalhes do pagamento
  - `createCustomer()`: Cria um cliente na API Asaas
  - `processBoleto()`: Processa pagamentos via boleto
  - `processCreditCard()`: Processa pagamentos via cartão de crédito
  - `processPix()`: Processa pagamentos via PIX

- `app/Http/Resources/PaymentResource.php`: Recurso para padronização das respostas da API

- `resources/views/layouts/app.blade.php`: Template principal do layout da aplicação
  - Inclui botão de logout para usuários autenticados

- `resources/views/payments/index.blade.php`: Página do formulário de pagamento
  - Formulário para coleta de dados pessoais
  - Seleção do método de pagamento (Boleto, Cartão de Crédito, PIX)
  - Campos específicos para cada método de pagamento

- `resources/views/payments/thank-you.blade.php`: Página de confirmação do pagamento
  - Exibe detalhes do pagamento
  - Para boleto: exibe link para visualização/impressão
  - Para PIX: exibe QR Code e código para cópia
  - Para cartão: exibe informações da transação

- `routes/web.php`: Rotas da aplicação
  - `/login`: Exibe o formulário de login e processa a autenticação
  - `/logout`: Encerra a sessão do usuário
  - `/payments`: Exibe o formulário de pagamento (protegido por autenticação)
  - `/payments/process`: Processa o pagamento (protegido por autenticação)
  - `/payments/thank-you`: Exibe a página de agradecimento (protegido por autenticação)

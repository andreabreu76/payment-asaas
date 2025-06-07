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
│   │   │   ├── HealthController.php   # Controlador para verificação de saúde do sistema
│   │   │   └── PaymentController.php  # Controlador principal para processamento de pagamentos
│   │   ├── Middleware/
│   │   │   └── AsaasLogContext.php    # Middleware para adicionar contexto aos logs
│   │   └── Resources/
│   │       └── PaymentResource.php    # Recurso para padronização das respostas da API
│   ├── Jobs/
│   │   └── ProcessPayment.php         # Job para processamento assíncrono de pagamentos
│   ├── Models/
│   │   ├── Payment.php                # Modelo para armazenamento de pagamentos
│   │   └── User.php                   # Modelo de usuário com suporte a JWT
│   └── Services/
│       └── AsaasLogger.php            # Serviço para logging estruturado da integração Asaas
├── config/
│   └── horizon.php                    # Configuração do Laravel Horizon
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
    ├── api.php                        # Definição das rotas da API
    └── web.php                        # Definição das rotas web
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
  - `process()`: Cria um registro de pagamento pendente e envia para a fila RabbitMQ
  - `thankYou()`: Exibe a página de agradecimento com detalhes do pagamento
  - `createCustomer()`: Cria um cliente na API Asaas
  - `preparePaymentData()`: Prepara os dados do pagamento com base no método selecionado
  - `getPaymentMethodName()`: Retorna o nome do método de pagamento

- `app/Jobs/ProcessPayment.php`: Job para processamento assíncrono de pagamentos
  - `handle()`: Processa o pagamento através da API Asaas e atualiza o status no banco de dados

- `app/Models/Payment.php`: Modelo para armazenamento de pagamentos
  - Armazena informações sobre pagamentos, incluindo status, método e dados de resposta da API

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

- `routes/web.php`: Rotas web da aplicação
  - `/login`: Exibe o formulário de login e processa a autenticação
  - `/logout`: Encerra a sessão do usuário
  - `/payments`: Exibe o formulário de pagamento (protegido por autenticação)
  - `/payments/process`: Processa o pagamento (protegido por autenticação)
  - `/payments/thank-you`: Exibe a página de agradecimento (protegido por autenticação)
  - `/horizon`: Dashboard do Laravel Horizon (protegido por autenticação)

- `routes/api.php`: Rotas da API
  - `/api/health`: Endpoint para verificação de saúde do sistema

### Monitoramento e Observabilidade

- `app/Http/Controllers/HealthController.php`: Controlador para verificação de saúde do sistema
  - `check()`: Verifica o status da aplicação, banco de dados e serviço de filas
  - `checkApp()`: Verifica o status da aplicação
  - `checkDatabase()`: Verifica a conexão com o banco de dados
  - `checkQueue()`: Verifica a conexão com o RabbitMQ

- `app/Http/Middleware/AsaasLogContext.php`: Middleware para adicionar contexto aos logs
  - `handle()`: Adiciona request_id, user_id, ip e user_agent ao contexto dos logs

- `app/Services/AsaasLogger.php`: Serviço para logging estruturado da integração Asaas
  - `info()`: Registra logs de nível info no canal 'asaas'
  - `error()`: Registra logs de nível error no canal 'asaas'
  - `warning()`: Registra logs de nível warning no canal 'asaas'
  - `debug()`: Registra logs de nível debug no canal 'asaas'

- `config/horizon.php`: Configuração do Laravel Horizon
  - Define configurações para monitoramento e gerenciamento das filas RabbitMQ
  - Configura workers, supervisores e limites de recursos

# Sistema de Processamento de Pagamentos - Asaas API

Este projeto é um sistema de processamento de pagamentos integrado ao ambiente de homologação do Asaas. O sistema permite que o cliente acesse uma página onde pode selecionar a opção de pagamento entre Boleto, Cartão ou Pix.

## Funcionalidades

- Autenticação JWT OAuth com tela de login
- Processamento de pagamentos com boleto, cartão de crédito e pix
- Validação de dados do formulário
- Exibição de boleto para pagamentos via boleto
- Exibição de QR Code e código Pix para pagamentos via Pix
- Mensagens amigáveis em caso de erro no processamento do pagamento

## Tecnologias Utilizadas

- PHP 8.x
- Laravel 10.x
- Bootstrap 5.x
- JWT (JSON Web Tokens) para autenticação
- Asaas API v3

## Requisitos

- PHP >= 8.2
- Composer
- Docker e Docker Compose (para o banco de dados)
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalação

### Método 1: Usando Docker (Recomendado)

1. Clone o repositório:
```bash
git clone https://github.com/andreabreu76/payment-asaas-api.git
cd payment-asaas-api
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Configure o arquivo .env com suas credenciais do Asaas Sandbox:
```
ASAAS_API_KEY=sua_api_key_aqui
```

4. Inicie os containers Docker:
```bash
docker-compose up -d
```

5. Instale as dependências:
```bash
docker-compose exec php composer install
```

6. Gere a chave da aplicação:
```bash
docker-compose exec php php artisan key:generate
```

7. Execute as migrações e seeders:
```bash
docker-compose exec php php artisan migrate --seed
```

8. Gere a chave JWT:
```bash
docker-compose exec php php artisan jwt:secret
```

9. Acesse o sistema em http://localhost

### Método 2: Instalação Local

1. Clone o repositório:
```bash
git clone https://github.com/andreabreu76/payment-asaas-api.git
cd payment-asaas-api
```

2. Instale as dependências:
```bash
composer install
```

3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

4. Configure o arquivo .env com suas credenciais do Asaas Sandbox:
```
ASAAS_API_KEY=sua_api_key_aqui
```

5. Gere a chave da aplicação:
```bash
php artisan key:generate
```

6. Inicie o banco de dados MySQL com Docker:
```bash
docker-compose up -d mysql
```

7. Configure o banco de dados no arquivo .env e execute as migrações e seeders:
```bash
php artisan migrate --seed
```

8. Gere a chave JWT:
```bash
php artisan jwt:secret
```

9. Use o arquivo php.ini personalizado para evitar avisos de depreciação:
```bash
php -c php.ini artisan serve
```

10. Acesse o sistema em http://localhost:8000

## Docker

Este projeto utiliza Docker para fornecer um ambiente de desenvolvimento completo e isolado. O Docker facilita a configuração do ambiente de desenvolvimento, garantindo que todos os desenvolvedores trabalhem com as mesmas versões de software e dependências.

### Pré-requisitos

Para utilizar o ambiente Docker, você precisa ter instalado:

- [Docker](https://docs.docker.com/get-docker/) (versão 20.10.0 ou superior)
- [Docker Compose](https://docs.docker.com/compose/install/) (versão 2.0.0 ou superior)

Você pode verificar as versões instaladas com os comandos:
```bash
docker --version
docker-compose --version
```

### Arquitetura do Ambiente Docker

O arquivo `docker-compose.yml` define quatro serviços principais:

1. **PHP**: Container PHP-FPM 8.2 para executar a aplicação Laravel
   - Baseado na imagem oficial `php:8.2-fpm`
   - Inclui extensões necessárias: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip
   - Composer pré-instalado para gerenciamento de dependências
   - Configuração personalizada via arquivo `php.ini`

2. **Nginx**: Servidor web que atua como proxy reverso para o PHP
   - Baseado na imagem `nginx:1.21-alpine`
   - Configurado para servir a aplicação Laravel
   - Expõe a porta 80 para acesso à aplicação

3. **MySQL**: Banco de dados MySQL 8.0 para armazenar os dados da aplicação
   - Utiliza a imagem oficial `mysql:8.0`
   - Dados persistentes através de um volume Docker
   - Configurável através de variáveis de ambiente no arquivo `.env`

4. **RabbitMQ**: Servidor de mensageria para processamento de filas
   - Utiliza a imagem oficial `rabbitmq:3-management`
   - Interface de gerenciamento web disponível
   - Configurável através de variáveis de ambiente no arquivo `.env`

### Configuração do Ambiente Docker

Para iniciar o ambiente completo, siga os passos abaixo:

1. Certifique-se de ter o Docker e o Docker Compose instalados em sua máquina
2. Clone o repositório e navegue até a pasta do projeto:
```bash
git clone https://github.com/andreabreu76/payment-asaas-api.git
cd payment-asaas-api
```
3. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```
4. Configure o arquivo .env com suas credenciais do Asaas Sandbox e ajuste as configurações de banco de dados se necessário
5. Construa e inicie os containers:
```bash
docker-compose build
docker-compose up -d
```
6. Instale as dependências do Composer:
```bash
docker-compose exec php composer install
```
7. Gere a chave da aplicação:
```bash
docker-compose exec php php artisan key:generate
```
8. Execute as migrações e seeders:
```bash
docker-compose exec php php artisan migrate --seed
```
9. Gere a chave JWT:
```bash
docker-compose exec php php artisan jwt:secret
```
10. Acesse o sistema em http://localhost

### Serviços e Portas

- **Aplicação Web**: http://localhost
- **MySQL**: acessível internamente via `mysql:3306` e externamente via `localhost:3306`
  - **Usuário**: laravel (configurável no .env via DB_USERNAME)
  - **Senha**: password (configurável no .env via DB_PASSWORD)
  - **Banco de dados**: laravel (configurável no .env via DB_DATABASE)
- **RabbitMQ**: 
  - **Management Interface**: http://localhost:15672
  - **AMQP**: acessível internamente via `rabbitmq:5672` e externamente via `localhost:5672`
  - **Usuário**: guest (configurável no .env via RABBITMQ_USER)
  - **Senha**: guest (configurável no .env via RABBITMQ_PASSWORD)

### Comandos Docker úteis para desenvolvimento

- Construir os containers (necessário na primeira execução ou após alterações nos Dockerfiles):
```bash
docker-compose build
```

- Iniciar os containers:
```bash
docker-compose up -d
```

- Iniciar os containers e ver logs em tempo real:
```bash
docker-compose up
```

- Verificar o status dos containers:
```bash
docker-compose ps
```

- Parar os containers sem remover os volumes:
```bash
docker-compose stop
```

- Parar e remover os containers (preserva os volumes):
```bash
docker-compose down
```

- Parar e remover os containers e volumes (remove todos os dados):
```bash
docker-compose down -v
```

- Ver logs dos containers:
```bash
docker-compose logs
```

- Ver logs de um container específico:
```bash
docker-compose logs php
```

- Ver logs em tempo real:
```bash
docker-compose logs -f
```

- Executar comandos Artisan:
```bash
docker-compose exec php php artisan <comando>
```

- Executar testes:
```bash
docker-compose exec php php artisan test
```

- Acessar o shell do container PHP:
```bash
docker-compose exec php bash
```

- Acessar o cliente MySQL:
```bash
docker-compose exec mysql mysql -u laravel -p laravel
```

### Configuração PHP

O projeto inclui um arquivo `php.ini` personalizado com configurações otimizadas para o Laravel. Este arquivo é automaticamente carregado pelo container PHP e inclui:

- Limites de upload aumentados (100M)
- Tempo de execução estendido (300 segundos)
- Limite de memória aumentado (512M)
- Configuração de timezone (UTC)

Você pode modificar estas configurações editando o arquivo `docker/php/php.ini`.

## Uso

### Autenticação

1. Acesse o sistema e você será redirecionado para a tela de login
2. Use as credenciais padrão:
   - Email: admin@example.com
   - Senha: admin
3. Após o login bem-sucedido, você será redirecionado para a página de pagamentos
4. Para sair do sistema, clique no botão "Logout" no canto superior direito

### Processamento de Pagamentos

1. Após o login, você verá o formulário de pagamento
2. Preencha o formulário com seus dados pessoais e o valor do pagamento
3. Selecione o método de pagamento (Boleto, Cartão de Crédito ou Pix)
4. Preencha os dados específicos do método de pagamento selecionado
5. Clique em "Finalizar Pagamento"
6. Você será redirecionado para a página de confirmação com as informações do pagamento

## Estrutura do Projeto

### Árvore de Diretórios

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

### Descrição dos Arquivos Principais

#### Autenticação

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

#### Processamento de Pagamentos

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

### Fluxo do Sistema de Pagamento

```mermaid
flowchart TD
    A[Usuário acessa o sistema] --> AA[Redirecionamento para tela de login]
    AA --> AB[Usuário insere credenciais]
    AB --> AC{Credenciais válidas?}
    AC -->|Não| AD[Exibe mensagem de erro]
    AD --> AB
    AC -->|Sim| AE[Gera token JWT]
    AE --> B[Exibição do formulário de pagamento]

    B --> C[Usuário preenche dados e seleciona método de pagamento]
    C --> D{Qual método de pagamento?}
    D -->|Boleto| E1[Processamento de Boleto]
    D -->|Cartão de Crédito| E2[Processamento de Cartão]
    D -->|PIX| E3[Processamento de PIX]

    E1 --> F1[API Asaas - Criação de Boleto]
    E2 --> F2[API Asaas - Processamento de Cartão]
    E3 --> F3[API Asaas - Geração de PIX]

    F1 --> G[Redirecionamento para página de agradecimento]
    F2 --> G
    F3 --> G

    G --> H{Qual foi o método?}
    H -->|Boleto| I1[Exibição do link do boleto]
    H -->|Cartão| I2[Exibição da confirmação do cartão]
    H -->|PIX| I3[Exibição do QR Code e código PIX]

    I1 --> J[Usuário finaliza o processo]
    I2 --> J
    I3 --> J

    J --> K[Usuário pode fazer logout]
    K --> AA
```

## Documentação da API Asaas

Para mais informações sobre a API do Asaas, consulte a [documentação oficial](https://asaasv3.docs.apiary.io/).

## Licença

Este projeto está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

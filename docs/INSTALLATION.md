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
# Instalação do Sistema de Processamento de Pagamentos

Este documento contém instruções detalhadas para instalar e configurar o sistema de processamento de pagamentos integrado ao Asaas.

## Requisitos

- PHP >= 8.2
- Composer
- Docker e Docker Compose (para o banco de dados)
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Método 1: Usando Docker (Recomendado)

1. Clone o repositório:
```bash
git clone https://github.com/andreabreu76/payment-asaas-api.git
cd payment-asaas-api
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Configure o arquivo .env com suas credenciais do Asaas Sandbox, configurações do RabbitMQ, NewRelic e Horizon:
```
ASAAS_API_KEY=sua_api_key_aqui
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=payments

# NewRelic Configuration
NEW_RELIC_LICENSE_KEY=your_license_key_here
NEW_RELIC_APP_NAME=Payment Asaas API
NEW_RELIC_DAEMON_ADDRESS=newrelic:31339

# Horizon Configuration
HORIZON_DOMAIN=null
HORIZON_PREFIX=payment_asaas_horizon:
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

## Método 2: Instalação Local

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

4. Configure o arquivo .env com suas credenciais do Asaas Sandbox, configurações do RabbitMQ, NewRelic e Horizon:
```
ASAAS_API_KEY=sua_api_key_aqui
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_QUEUE=payments

# NewRelic Configuration
NEW_RELIC_LICENSE_KEY=your_license_key_here
NEW_RELIC_APP_NAME=Payment Asaas API
NEW_RELIC_DAEMON_ADDRESS=newrelic:31339

# Horizon Configuration
HORIZON_DOMAIN=null
HORIZON_PREFIX=payment_asaas_horizon:
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

10. Em um terminal separado, inicie o worker para processar a fila de pagamentos:
```bash
php -c php.ini artisan queue:work rabbitmq --queue=payments
```

11. Em outro terminal separado, inicie o Laravel Horizon para monitoramento das filas:
```bash
php -c php.ini artisan horizon
```

12. Acesse o sistema em http://localhost:8000
13. Acesse o dashboard do Horizon em http://localhost:8000/horizon (requer autenticação)

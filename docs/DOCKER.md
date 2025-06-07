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
# Docker Configuration

Este documento contém informações detalhadas sobre a configuração Docker do projeto.

## Pré-requisitos

Para utilizar o ambiente Docker, você precisa ter instalado:

- [Docker](https://docs.docker.com/get-docker/) (versão 20.10.0 ou superior)
- [Docker Compose](https://docs.docker.com/compose/install/) (versão 2.0.0 ou superior)

Você pode verificar as versões instaladas com os comandos:
```bash
docker --version
docker-compose --version
```

## Arquitetura do Ambiente Docker

O arquivo `docker-compose.yml` define cinco serviços principais:

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

5. **Worker**: Container para processamento assíncrono de pagamentos
   - Baseado na mesma imagem do container PHP
   - Executa o comando `php artisan queue:work rabbitmq --queue=payments`
   - Processa as mensagens da fila RabbitMQ em background
   - Compartilha o mesmo volume do container PHP para acesso ao código

## Serviços e Portas

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

## Comandos Docker úteis para desenvolvimento

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

## Configuração PHP

O projeto inclui um arquivo `php.ini` personalizado com configurações otimizadas para o Laravel. Este arquivo é automaticamente carregado pelo container PHP e inclui:

- Limites de upload aumentados (100M)
- Tempo de execução estendido (300 segundos)
- Limite de memória aumentado (512M)
- Configuração de timezone (UTC)

Você pode modificar estas configurações editando o arquivo `docker/php/php.ini`.

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

# Sistema de Processamento de Pagamentos - Asaas API

Este projeto é um sistema de processamento de pagamentos integrado ao ambiente de homologação do Asaas. O sistema permite que o cliente acesse uma página onde pode selecionar a opção de pagamento entre Boleto, Cartão ou Pix.

## Visão Geral

Este projeto foi desenvolvido como um portfólio para demonstrar capacidades técnicas e entendimento sobre a stack e recursos utilizados. O sistema implementa uma integração completa com a API Asaas para processamento de pagamentos, com foco em boas práticas de desenvolvimento, testes automatizados e integração contínua.

## Stack Tecnológica

- **Backend**: PHP 8.2, Laravel 10.x
- **Frontend**: Bootstrap 5.x, Blade Templates
- **Autenticação**: JWT (JSON Web Tokens)
- **Banco de Dados**: MySQL 8.0
- **Mensageria**: RabbitMQ
- **Containerização**: Docker e Docker Compose
- **API de Pagamentos**: Asaas API v3
- **Testes**: PHPUnit
- **CI/CD**: GitHub Actions

## Funcionalidades Principais

- Autenticação JWT OAuth com tela de login
- Processamento de pagamentos com múltiplos métodos:
  - Boleto bancário
  - Cartão de crédito
  - PIX
- Validação de dados do formulário
- Exibição de boleto para pagamentos via boleto
- Exibição de QR Code e código Pix para pagamentos via Pix
- Mensagens amigáveis em caso de erro no processamento do pagamento
- Testes automatizados para todas as funcionalidades principais
- Integração contínua via GitHub Actions

## Fluxo do Sistema de Pagamento

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
    D -->|Boleto| E1[Criação de registro de pagamento pendente]
    D -->|Cartão de Crédito| E2[Criação de registro de pagamento pendente]
    D -->|PIX| E3[Criação de registro de pagamento pendente]

    E1 --> F1[Envio para fila RabbitMQ]
    E2 --> F1
    E3 --> F1

    F1 --> G[Redirecionamento para página de agradecimento com status pendente]

    G --> H[Worker processa pagamento em background]
    H --> I[API Asaas - Processamento do pagamento]
    I --> J[Atualização do status do pagamento no banco de dados]

    G --> K[Usuário pode verificar status posteriormente]
    K --> L[Usuário pode fazer logout]
    L --> AA
```

## Processamento Assíncrono de Pagamentos

O sistema utiliza processamento assíncrono de pagamentos através do RabbitMQ para melhorar a experiência do usuário e a resiliência do sistema:

1. Quando um usuário submete um pagamento, o sistema:
   - Cria um registro de pagamento com status "pendente" no banco de dados
   - Envia uma mensagem para a fila RabbitMQ com os detalhes do pagamento
   - Redireciona o usuário para a página de agradecimento imediatamente

2. Um worker em background:
   - Consome as mensagens da fila RabbitMQ
   - Processa o pagamento através da API Asaas
   - Atualiza o status do pagamento no banco de dados

Benefícios desta abordagem:
- Resposta mais rápida para o usuário
- Maior resiliência a falhas temporárias da API de pagamento
- Capacidade de processar pagamentos em lote
- Melhor escalabilidade do sistema

## Fluxo de CI/CD

```mermaid
flowchart TD
    A[Desenvolvedor cria branch] --> B[Implementa mudanças]
    B --> C[Executa testes locais]
    C --> D[Cria Pull Request]
    D --> E{GitHub Actions CI}
    E -->|Falha| F[Corrige problemas]
    F --> C
    E -->|Sucesso| G[Code Review]
    G -->|Aprovado| H[Merge para main]
    G -->|Rejeitado| F
```

## Documentação Detalhada

Para informações mais detalhadas sobre o projeto, consulte os seguintes documentos:

- [Instruções de Instalação](docs/INSTALLATION.md)
- [Configuração Docker](docs/DOCKER.md)
- [Guia de Uso](docs/USAGE.md)
- [Testes e Integração Contínua](docs/TESTING.md)
- [Estrutura do Projeto](docs/STRUCTURE.md)

## Documentação da API Asaas

Para mais informações sobre a API do Asaas, consulte a [documentação oficial](https://asaasv3.docs.apiary.io/).

## Licença

Este projeto está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

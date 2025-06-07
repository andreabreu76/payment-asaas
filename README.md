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

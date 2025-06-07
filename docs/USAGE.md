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
# Guia de Uso do Sistema

Este documento contém instruções detalhadas sobre como utilizar o sistema de processamento de pagamentos.

## Autenticação

1. Acesse o sistema e você será redirecionado para a tela de login
2. Use as credenciais padrão:
   - Email: admin@example.com
   - Senha: admin
3. Após o login bem-sucedido, você será redirecionado para a página de pagamentos
4. Para sair do sistema, clique no botão "Logout" no canto superior direito

## Processamento de Pagamentos

1. Após o login, você verá o formulário de pagamento
2. Preencha o formulário com seus dados pessoais e o valor do pagamento
3. Selecione o método de pagamento (Boleto, Cartão de Crédito ou Pix)
4. Preencha os dados específicos do método de pagamento selecionado
5. Clique em "Finalizar Pagamento"
6. Você será redirecionado para a página de confirmação com as informações do pagamento

### Pagamento via Boleto

Ao selecionar o método de pagamento "Boleto":

1. Preencha os dados pessoais e o valor do pagamento
2. Clique em "Finalizar Pagamento"
3. Na página de confirmação, você verá um link para visualizar e imprimir o boleto
4. O boleto terá um prazo de vencimento e instruções para pagamento

### Pagamento via Cartão de Crédito

Ao selecionar o método de pagamento "Cartão de Crédito":

1. Preencha os dados pessoais e o valor do pagamento
2. Preencha os dados do cartão:
   - Nome do titular
   - Número do cartão
   - Mês de expiração
   - Ano de expiração
   - Código de segurança (CCV)
   - CEP
   - Número do endereço
3. Clique em "Finalizar Pagamento"
4. Na página de confirmação, você verá os detalhes da transação

### Pagamento via PIX

Ao selecionar o método de pagamento "PIX":

1. Preencha os dados pessoais e o valor do pagamento
2. Clique em "Finalizar Pagamento"
3. Na página de confirmação, você verá:
   - QR Code para pagamento
   - Código PIX para copiar e colar
4. Use o aplicativo do seu banco para escanear o QR Code ou colar o código PIX

## Fluxo do Sistema de Pagamento

O sistema segue o seguinte fluxo para processamento de pagamentos:

1. Autenticação do usuário
2. Preenchimento do formulário de pagamento
3. Seleção do método de pagamento
4. Criação de um registro de pagamento pendente no banco de dados
5. Envio do pagamento para processamento assíncrono via RabbitMQ
6. Exibição imediata da página de confirmação com status pendente
7. Processamento do pagamento em background pelo worker
8. Atualização do status do pagamento no banco de dados

### Processamento Assíncrono

O sistema utiliza processamento assíncrono para melhorar a experiência do usuário:

1. Quando você finaliza um pagamento, o sistema:
   - Cria um registro de pagamento com status "pendente"
   - Envia os dados para processamento em background
   - Exibe imediatamente a página de confirmação

2. Enquanto isso, um worker processa o pagamento em background:
   - Comunica-se com a API Asaas
   - Atualiza o status do pagamento no banco de dados

Benefícios deste modelo:
- Resposta mais rápida para o usuário
- Maior resiliência a falhas temporárias da API de pagamento
- Melhor experiência do usuário em conexões lentas

## Monitoramento e Observabilidade

### Health Check API

O sistema fornece um endpoint de health check para monitorar o status da aplicação e seus serviços:

- **Endpoint**: `/api/health`
- **Método**: GET
- **Resposta**: JSON com status da aplicação, banco de dados e serviço de filas

Exemplo de resposta:
```json
{
  "status": "ok",
  "timestamp": "2023-06-01T12:34:56+00:00",
  "services": {
    "app": {
      "status": "ok",
      "version": "1.0.0"
    },
    "database": {
      "status": "ok",
      "connection": "mysql"
    },
    "queue": {
      "status": "ok",
      "connection": "rabbitmq"
    }
  }
}
```

### Logging Estruturado

O sistema utiliza logging estruturado para a integração com o Asaas, facilitando o diagnóstico de problemas:

- Todos os logs relacionados à integração Asaas são armazenados em `storage/logs/asaas.log`
- Os logs incluem contexto adicional como:
  - ID da requisição (request_id)
  - ID do usuário
  - Endereço IP
  - User Agent

Para desenvolvedores, a classe `AsaasLogger` está disponível para registrar eventos:

```php
use App\Services\AsaasLogger;

// Exemplos de uso
AsaasLogger::info('Pagamento iniciado', ['payment_id' => $id]);
AsaasLogger::error('Falha no processamento', ['payment_id' => $id, 'error' => $e->getMessage()]);
AsaasLogger::debug('Dados enviados para API', ['payload' => $data]);
```

### Laravel Horizon

O Laravel Horizon fornece um dashboard para monitoramento e gerenciamento das filas RabbitMQ:

1. Acesse o dashboard em `/horizon` (requer autenticação)
2. No dashboard você pode:
   - Visualizar jobs em execução, pendentes e falhos
   - Monitorar a performance dos workers
   - Visualizar métricas de processamento
   - Reiniciar jobs falhos
   - Pausar e retomar workers

### NewRelic Monitoring

O sistema está integrado com NewRelic para monitoramento de performance:

- Métricas de performance são automaticamente enviadas para o NewRelic
- O dashboard do NewRelic mostra:
  - Tempo de resposta da aplicação
  - Taxa de erros
  - Throughput
  - Uso de recursos
  - Transações mais lentas
  - Erros mais frequentes

Para acessar o dashboard do NewRelic, faça login na sua conta NewRelic e selecione a aplicação configurada no arquivo `.env`.

## Documentação da API Asaas

Para mais informações sobre a API do Asaas, consulte a [documentação oficial](https://asaasv3.docs.apiary.io/).

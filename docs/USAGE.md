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

## Documentação da API Asaas

Para mais informações sobre a API do Asaas, consulte a [documentação oficial](https://asaasv3.docs.apiary.io/).

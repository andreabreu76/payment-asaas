# Sistema de Processamento de Pagamentos - Asaas API

Este projeto é um sistema de processamento de pagamentos integrado ao ambiente de homologação do Asaas. O sistema permite que o cliente acesse uma página onde pode selecionar a opção de pagamento entre Boleto, Cartão ou Pix.

## Funcionalidades

- Processamento de pagamentos com boleto, cartão de crédito e pix
- Validação de dados do formulário
- Exibição de boleto para pagamentos via boleto
- Exibição de QR Code e código Pix para pagamentos via Pix
- Mensagens amigáveis em caso de erro no processamento do pagamento

## Tecnologias Utilizadas

- PHP 8.x
- Laravel 10.x
- Bootstrap 5.x
- Asaas API v3

## Requisitos

- PHP >= 8.1
- Composer
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalação

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

6. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

7. Acesse o sistema em http://localhost:8000

## Uso

1. Acesse a página inicial do sistema
2. Preencha o formulário com seus dados pessoais e o valor do pagamento
3. Selecione o método de pagamento (Boleto, Cartão de Crédito ou Pix)
4. Preencha os dados específicos do método de pagamento selecionado
5. Clique em "Finalizar Pagamento"
6. Você será redirecionado para a página de confirmação com as informações do pagamento

## Estrutura do Projeto

- `app/Http/Controllers/PaymentController.php`: Controlador responsável pelo processamento de pagamentos
- `app/Http/Resources/PaymentResource.php`: Recurso para padronização das respostas da API
- `resources/views/payments/index.blade.php`: Página do formulário de pagamento
- `resources/views/payments/thank-you.blade.php`: Página de confirmação do pagamento
- `routes/web.php`: Rotas da aplicação

## Documentação da API Asaas

Para mais informações sobre a API do Asaas, consulte a [documentação oficial](https://asaasv3.docs.apiary.io/).

## Licença

Este projeto está licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

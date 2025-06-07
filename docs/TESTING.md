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
# Testes e Integração Contínua

Este documento contém informações sobre os testes automatizados e o processo de integração contínua (CI) do projeto.

## Testes Automatizados

O projeto utiliza o PHPUnit para testes automatizados, com suporte às funcionalidades de testes do Laravel como RefreshDatabase e WithFaker.

### Estrutura de Testes

Os testes estão organizados nas seguintes pastas:

- `tests/Feature`: Testes de funcionalidades que simulam requisições HTTP e testam o comportamento da aplicação como um todo
- `tests/Unit`: Testes unitários que testam componentes individuais da aplicação

### Principais Testes

#### Testes de Autenticação

Os testes de autenticação verificam:
- Exibição da página de login
- Login com credenciais válidas
- Rejeição de credenciais inválidas
- Funcionalidade de logout

#### Testes de Processamento de Pagamentos

Os testes de processamento de pagamentos verificam:
- Exibição do formulário de pagamento
- Processamento de pagamentos via boleto
- Processamento de pagamentos via cartão de crédito
- Processamento de pagamentos via PIX
- Validação de dados de entrada
- Exibição da página de confirmação de pagamento

### Executando os Testes

Para executar todos os testes:

```bash
php artisan test
```

Ou, se estiver usando Docker:

```bash
docker-compose exec php php artisan test
```

Para executar um grupo específico de testes:

```bash
php artisan test --filter=AuthControllerTest
```

## Integração Contínua (CI)

O projeto utiliza GitHub Actions para integração contínua. O workflow de CI é executado automaticamente em cada pull request para a branch principal (main).

### Workflow de Pull Request

O arquivo `.github/workflows/pull-request.yml` define o workflow de CI que é executado em cada pull request. Este workflow:

1. Configura o ambiente PHP 8.1 com as extensões necessárias
2. Faz checkout do código
3. Copia o arquivo .env de exemplo
4. Instala as dependências do Composer
5. Gera a chave da aplicação
6. Configura as permissões de diretório
7. Cria um banco de dados SQLite para testes
8. Executa todos os testes unitários e de feature

### Benefícios do CI

- Detecção precoce de problemas: os testes são executados automaticamente em cada pull request
- Garantia de qualidade: apenas código que passa em todos os testes pode ser mesclado à branch principal
- Feedback rápido: os desenvolvedores recebem feedback imediato sobre a qualidade do código
- Documentação viva: os testes servem como documentação do comportamento esperado do sistema

### Boas Práticas

1. Sempre execute os testes localmente antes de enviar um pull request
2. Adicione testes para novas funcionalidades ou correções de bugs
3. Mantenha a cobertura de testes alta
4. Verifique os resultados do CI antes de mesclar pull requests

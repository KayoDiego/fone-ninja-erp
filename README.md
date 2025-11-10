# Fone Ninja ERP

Sistema de gerenciamento de estoque desenvolvido em Laravel 10 + Vue 3 + Vite.

## Funcionalidades

- Cadastro e listagem de produtos com custo médio automático
- Registro de compras com atualização de estoque e custo médio ponderado
- Registro de vendas com validação de estoque e cálculo de lucro
- Cancelamento de vendas com reversão de estoque
- Histórico completo de transações

## Como rodar a aplicação

1. Pré-requisitos
   - PHP 8.2+
   - Composer
   - Node.js 18+ e npm
   - Banco de dados compatível (MySQL ou MariaDB)

2. Instalar dependências
   composer install
   npm install

3. Configurar variáveis de ambiente
   cp .env.example .env
   php artisan key:generate
   Ajuste as credenciais do banco no arquivo .env

4. Executar migrações
   php artisan migrate

5. Iniciar os servidores
   php artisan serve
   npm run dev
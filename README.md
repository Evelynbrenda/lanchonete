# Lanchonete do Márcio - Sistema Web de Pedidos

Sistema web de pedidos para lanchonete, com cardápio online, checkout via WhatsApp e painel administrativo para operação diária.

## Tecnologias
- Laravel (Blade)
- TailwindCSS
- JavaScript puro
- MySQL
- Docker / Docker Compose

## Funcionalidades principais
- Cardápio público com busca, filtros e categorias.
- Carrinho em `localStorage`.
- Checkout com:
  - tipo de atendimento (entrega/retirada)
  - cálculo de taxa de entrega por rota
  - envio da mensagem para WhatsApp
- Salvamento de pedido no banco antes do envio no WhatsApp.
- Painel admin com:
  - Dashboard
  - Pedidos (filtros + atualização de status)
  - Produtos
  - Categorias (com regras de adicionais/opções/tamanhos)
  - Conteúdo do site (institucional, avisos, promoções, funcionamento, rotas)

## Fluxo operacional
1. Cliente acessa `/cardapio`.
2. Monta pedido e vai para `/checkout`.
3. Sistema valida dados e grava pedido em `/pedidos`.
4. WhatsApp abre com mensagem formatada do pedido.
5. Admin acompanha e atualiza status em `/admin/pedidos`.

## Status de pedido
- `novo`
- `preparando`
- `pronto`
- `saiu_para_entrega`
- `entregue`
- `cancelado`

## Requisitos locais
- PHP 8.1+
- Composer
- Node (opcional, se for compilar assets)
- MySQL 8+

## Instalação (sem Docker)
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Instalação com Docker
```bash
cp .env.example .env
docker compose run --rm app php artisan key:generate
docker compose up -d --build
docker compose exec app php artisan migrate --seed --force
```

App: `http://localhost:8000`

## Acesso administrativo
- URL: `/painel`
- A autenticação usa senha definida por variável de ambiente.

### Importante
Defina no `.env`:
```env
ADMIN_PANEL_PASSWORD=sua_senha_forte
```

## Variáveis importantes
- `APP_ENV=production` (produção)
- `APP_DEBUG=false` (produção)
- `APP_TIMEZONE=America/Bahia`
- `APP_URL=https://seu-dominio.com`
- `ADMIN_PANEL_PASSWORD=...`

## Segurança e boas práticas para deploy
- Nunca subir `.env` para o repositório.
- Usar senha forte para admin.
- Garantir HTTPS no domínio.
- Rodar cache de config/rotas em produção:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
- Rodar migrations em produção com atenção:
```bash
php artisan migrate --force
```

## Comandos úteis
```bash
# limpar caches
php artisan optimize:clear

# logs
php artisan pail
# ou
 tail -f storage/logs/laravel.log
```

## Estrutura resumida
- `resources/views/cardapio/*` -> telas públicas (cardápio/checkout)
- `resources/views/admin/*` -> painel administrativo
- `app/Http/Controllers/*` -> regras de fluxo
- `app/Models/*` -> entidades (Pedido, Produto, Categoria, etc.)
- `database/migrations/*` -> estrutura do banco

## Observações
- O carrinho fica no navegador do cliente (`localStorage`).
- O total do pedido é recalculado no backend para segurança.
- Loja pode operar com regras de funcionamento (dias/horários) e indisponibilidade por produto.

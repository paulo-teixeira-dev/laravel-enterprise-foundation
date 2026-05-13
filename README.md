# Laravel Enterprise Foundation

Base arquitetural reutilizável para aplicações corporativas desenvolvida com Laravel, contendo autenticação, autorização baseada em permissões (RBAC), auditoria de logs, gerenciamento de usuários, pessoas e contatos, arquitetura modular e estrutura preparada para escalabilidade, APIs REST, Docker e CI/CD.

O projeto fica em `src/` e a infraestrutura Docker fica na raiz do repositório. A API usa Laravel, PostgreSQL, Laravel Passport, Spatie Permission e Spatie Activitylog.

## Requisitos

- Docker e Docker Compose
- PHP 8.3+ (execução local sem Docker)
- Composer (execução local sem Docker)
- PostgreSQL acessível pela aplicação

> A imagem Docker do projeto usa PHP 8.4 FPM. O `composer.json` exige PHP `^8.3`.

## Configuração do ambiente

Copie os arquivos de ambiente de desenvolvimento/qualidade/produção. Exemplo:

```bash
cp docker/environments/.env.dev .env
cp src/environments/.env.dev src/.env
```

Revise o arquivo `src/.env` antes de iniciar a aplicação, principalmente:

```env
APP_URL=http://localhost
DB_CONNECTION='pgsql'
DB_HOST='ip-address'
DB_PORT='5432'
DB_DATABASE='laravel_enterprise_foundation'
DB_SCHEMA='lara'
DB_USERNAME='postgres'
DB_PASSWORD='1234567'
```

O arquivo `.env` da raiz controla o Docker Compose, incluindo o nome do projeto, a rede e a porta HTTP. No ambiente de desenvolvimento, a API fica disponível em:

```text
http://localhost:7700
```

## Subindo com Docker

Na raiz do repositório:

```bash
docker compose up -d --build
```

Entre no container PHP para executar comandos Laravel:

```bash
docker compose exec laravel-enterprise-foundation-php bash
```

## Banco de dados e timezone

A aplicação está configurada para usar UTC em `config/app.php` e na conexão PostgreSQL em `config/database.php`.

Para confirmar o timezone do banco:

```bash
php artisan tinker
```

No Tinker:

```php
DB::select("SELECT now(), current_setting('timezone') as timezone");
```

## Configurando dependências do projeto.

Dentro do container:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan passport:keys
php artisan passport:client --personal
php artisan passport:client --password
php artisan db:seed --class=InitialSeeder
```

## Execução local sem Docker

Dentro de `src/`:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan passport:keys
php artisan passport:client --personal
php artisan passport:client --password
php artisan db:seed --class=InitialSeeder
composer run dev
```

O script `composer run dev` inicia, em paralelo, o servidor Laravel, fila.

## Autenticação

A API usa Laravel Passport.

Comandos úteis:

```bash
php artisan passport:keys
php artisan passport:client --personal
php artisan passport:client --password
```

Depois de gerar os clients, guarde os dados retornados pelo Passport conforme a necessidade do ambiente.

## Endpoints principais

Todas as rotas da API estão sob o prefixo `/api/v1`.

## Comandos úteis

Execute os comandos abaixo dentro de `src/` ou dentro do container PHP em `/var/www`.

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed --class=InitialSeeder
```

## Padrão de camadas

O projeto organiza a regra de negócio em controllers, services e repositories.

Exemplo para criar um novo recurso:

```bash
php artisan make:controller UserController
php artisan make:class Services/UserService
php artisan make:class Repositories/UserRepository
```

## Observações

- Não versionar arquivos `.env` com credenciais reais.
- Ajustar `DB_HOST`, `DB_SCHEMA` e credenciais antes de rodar migrations.
- Rodar `InitialSeeder` após migrations para criar os dados básicos de configuração.
- Gerar as chaves e clients do Passport em cada ambiente.


## Features

- Autenticação com Laravel Passport
- Controle de acesso baseado em papéis e permissões (RBAC) com Spatie Permission.
- Auditoria de logs com Spatie Activitylog
- Gerenciamento de usuários
- Gerenciamento de pessoas e contatos
- Arquitetura em camadas
- API REST versionada
- Ambiente Docker
- Estrutura preparada para CI/CD

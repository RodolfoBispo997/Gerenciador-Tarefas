Gerenciador de Tarefas

Sistema completo de gestão de tarefas com arquitetura desacoplada, composto por API REST em Laravel 11 e interface moderna em Vue.js.

O projeto foi desenvolvido com foco em organização de código, autenticação segura e separação clara entre back-end e front-end.

🏗️ Arquitetura

O sistema está dividido em duas aplicações:

📌 API (Laravel 11)

Autenticação utilizando Laravel Sanctum

CRUD completo de tarefas

Controle de acesso com Policies

Migrations e Seeders organizados

Estrutura MVC seguindo boas práticas

Principais camadas:

Controllers

Models

Policies

Migrations

Routes (api.php)

🎨 Front-end (Vue.js)

SPA estruturada com Vue

Gerenciamento de rotas

Controle de autenticação

Comunicação com API via Axios

Organização por componentes e layouts

🔐 Funcionalidades

Registro e login de usuários

Autenticação baseada em token

CRUD de tarefas

Controle de permissões

Validação de dados

Separação entre usuários e tarefas

🛠️ Tecnologias Utilizadas
Backend

PHP 8+

Laravel 11

Laravel Sanctum

MySQL

Eloquent ORM

Frontend

Vue.js

Vue Router

Pinia (ou store utilizada)

Axios

TailwindCSS

🚀 Como Executar
Backend
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

Frontend
cd app
npm install
npm run dev

📌 Objetivo do Projeto

Demonstrar domínio em desenvolvimento full stack com foco em APIs REST, autenticação segura e arquitetura desacoplada.

🔮 Melhorias Futuras

Implementação de testes automatizados

Dockerização do ambiente

Documentação de API com Swagger

Paginação e filtros avançados

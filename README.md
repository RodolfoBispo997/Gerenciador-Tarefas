# 🗂 Gerenciador de Tarefas

API + Dashboard completa para gerenciamento de tarefas com backend em **Laravel 11** e frontend em **Vue.js**.  
Projeto com arquitetura desacoplada, autenticação segura e interface intuitiva.

---

## 🚀 Tecnologias

**Backend**
- PHP 8+
- Laravel 11
- Laravel Sanctum (autenticação via token)
- MySQL (banco de dados)

**Frontend**
- Vue.js
- Vue Router
- Pinia (ou gerenciamento de estado)
- Axios (requisições HTTP)
- TailwindCSS / CSS customizado

---

## 🧠 Visão Geral

Esse projeto é um sistema full-stack que permite:

✔ Registro e login de usuário  
✔ Autenticação segura com tokens  
✔ CRUD completo de tarefas  
✔ Dashboard com visualização dinâmica  
✔ Separação clara entre frontend e backend  
✔ Arquitetura organizada para manutenção

Ele foi criado com foco em **boas práticas de engenharia de software, API REST e experiência de usuário**.

---

## 🗂 Estrutura do Projeto

<img width="789" height="325" alt="image" src="https://github.com/user-attachments/assets/e9e39103-7df1-4e38-a40d-d61a7732269d" />


---

## 🛠 Funcionalidades

### 🧑‍💻 Backend
- Cadastro e Login de usuários
- Tokens de autenticação com Sanctum
- Rotas protegidas por middleware
- Controle de acesso com **policies**
- Endpoints REST para tarefas

### 📊 Frontend
- Dashboard responsivo
- Tela de login e cadastro
- Listagem, criação, edição e exclusão de tarefas
- Navegação SPA com Vue Router
- Comunicação com API via Axios

---

## 🏁 Como Rodar o Projeto

### 💻 Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve


📱 Frontend
cd frontend
npm install
npm run dev


📌 Observações

As credenciais para testes estão disponíveis nos seeders

A API segue padrões RESTful

O frontend consome a API com Axios e mantém estado com Pinia

👨‍💻 O que esse projeto demonstra

✔ Capacidade de criar APIs REST
✔ Separação clara entre front e back
✔ Autenticação e segurança
✔ Estrutura de projeto organizada
✔ Boas práticas de Laravel + Vue.js
✔ Preparação para evoluir o projeto

📈 Melhorias Futuras

✨ Dockerização
✨ Testes automatizados (PHPUnit / Vitest)
✨ Documentação de API com Swagger ou OpenAPI
✨ Integração contínua (CI/CD)

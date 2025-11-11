# Sistema de Mensagens Anônimas Internas

Este repositório contém uma aplicação Laravel destinada a receber sugestões, elogios, denúncias e demais manifestações de forma anônima, permitindo que colaboradores contribuam para a melhoria contínua da empresa.

## Sumário

-   Visão geral
-   Principais funcionalidades
-   Arquitetura e tecnologias
-   Requisitos
-   Guia de execução
-   Uso via Docker
-   Testes
-   Estrutura de dados
-   Convenções de desenvolvimento
-   Como contribuir
-   Suporte e contato
-   Licença

## Visão geral

-   **Objetivo**: facilitar a comunicação anônima entre colaboradores e a área responsável pelas manifestações internas.
-   **Público-alvo**: equipes de RH, compliance e lideranças que precisam centralizar feedbacks e acompanhar indicadores.
-   **Benefícios**: anonimato garantido, categorização das mensagens, painel administrativo para acompanhamento e resposta.

## Principais funcionalidades

-   Envio de mensagens anônimas com anexos opcionais.
-   Seleção do tipo de mensagem (elogio, sugestão, reclamação, etc.).
-   Interface pública simplificada e responsiva.
-   Painel administrativo com autenticação, listagem e detalhamento das mensagens recebidas.
-   Gestão de usuários e tipos de mensagem por meio da área administrativa.
-   Notificações visuais para facilitar o acompanhamento de novas mensagens.

## Arquitetura e tecnologias

-   **Backend**: Laravel 11 (PHP 8.2) com Eloquent ORM.
-   **Frontend**: Vue 3 + Inertia.js + Vite.
-   **Banco de dados**: SQLite (ambiente local) — compatível com MySQL/PostgreSQL em produção.
-   **Estilização**: Tailwind CSS (via Vite) e componentes Vue personalizados.
-   **Autenticação**: padrão Laravel com proteção CSRF e middleware de administração.

## Requisitos

-   PHP 8.2+ com extensões `sqlite3`, `openssl`, `mbstring`, `pdo`.
-   Composer 2.x.
-   Node.js 20+ e npm.
-   SQLite (ou outro SGDB suportado pelo Laravel).
-   Opcional: Docker Engine e Docker Compose v2.

## Guia de execução

1. **Configuração inicial**
    ```bash
    cp .env.example .env
    composer install
    npm install
    php artisan key:generate
    ```
2. **Banco de dados e dados iniciais**
    ```bash
    php artisan migrate --seed
    ```
3. **Servidores de aplicação**
    ```bash
    php artisan serve      # backend em http://localhost:8000
    npm run dev            # assets front-end com hot reload
    ```
4. **Credenciais padrão**
    - Usuário administrador gerado pelo seeder: `admin@example.com`
    - Senha inicial: `password` (exigirá troca no primeiro acesso)

Personalize as variáveis no arquivo `.env` conforme necessário (configuração de e-mail, armazenamento de arquivos, driver de cache etc.).

## Uso via Docker

```bash
docker compose up -d
```

Após finalizar a sessão de desenvolvimento, encerre os serviços e limpe os recursos:

```bash
docker compose down
docker system prune -a --volumes -f
```

## Testes

-   Testes backend: `php artisan test`
-   Testes front-end (quando configurados): `npm run test`
-   Verificação estática opcional: `./vendor/bin/phpstan analyse`

## Estrutura de dados

-   `messages`: armazena cada manifestação anônima, linkando opcionalmente arquivos anexos.
-   `message_types`: tipificação das mensagens (elogio, sugestão, denúncia etc.).
-   `users`: usuários autenticados e administradores do painel.

## Convenções de desenvolvimento

-   Padronize commits usando o formato `tipo: descrição`.
-   Utilize `phpcs` e `eslint` (quando configurados) antes de submeter alterações.
-   Prefira Pull Requests pequenos e com descrição objetiva.

## Como contribuir

-   Abra uma issue descrevendo a proposta.
-   Crie uma branch: `git checkout -b feature/nome-da-feature`.
-   Envie os commits e abra um Pull Request detalhando mudanças e testes realizados.

## Suporte e contato

-   Responsável: Equipe de TI / RH.
-   E-mail: `contato@example.com`.
-   Alternative: abra uma issue pública no repositório.

## Licença

Distribuído sob a licença MIT. Consulte o arquivo `LICENSE` para mais detalhes.

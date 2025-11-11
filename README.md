# Sistema de Mensagens Anônimas Internas

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP 8.2](https://img.shields.io/badge/php-8.2-blue.svg)](https://www.php.net/)
[![Laravel 11](https://img.shields.io/badge/laravel-11-red.svg)](https://laravel.com/)
[![Vue 3](https://img.shields.io/badge/vue-3-green.svg)](https://vuejs.org/)
[![Inertia.js](https://img.shields.io/badge/inertia.js-enabled-purple.svg)](https://inertiajs.com/)

Plataforma para coleta de manifestações anônimas (elogios, sugestões, reclamações, denúncias) criada para fomentar um ambiente de trabalho transparente e colaborativo.

## Índice

-   [Contexto](#contexto)
-   [Principais Recursos](#principais-recursos)
-   [Arquitetura e Tecnologias](#arquitetura-e-tecnologias)
-   [Fluxo da Aplicação](#fluxo-da-aplicação)
-   [Requisitos](#requisitos)
-   [Configurando o Ambiente](#configurando-o-ambiente)
    -   [Instalação Local](#instalação-local)
    -   [Configuração do Banco](#configuração-do-banco)
    -   [Execução dos Servidores](#execução-dos-servidores)
-   [Execução com Docker](#execução-com-docker)
-   [Testes e Garantia de Qualidade](#testes-e-garantia-de-qualidade)
-   [Estrutura do Projeto](#estrutura-do-projeto)
-   [Boas Práticas de Desenvolvimento](#boas-práticas-de-desenvolvimento)
-   [Como Contribuir](#como-contribuir)
-   [Perguntas Frequentes](#perguntas-frequentes)
-   [Suporte](#suporte)
-   [Licença](#licença)

## Contexto

-   **Problema**: colaboradores raramente expõem feedbacks sensíveis por receio de represálias.
-   **Solução**: canal anônimo centralizado com painel administrativo seguro.
-   **Benefícios**: estímulo a melhoria contínua, indicadores de clima organizacional e rastreabilidade das ações internas.

## Principais Recursos

-   Envio anônimo de mensagens com suporte a anexos.
-   Classificação por tipo (`elogio`, `sugestão`, `reclamação`, `denúncia`, personalizáveis).
-   Painel administrativo autenticado com filtros, paginação e detalhamento das mensagens.
-   Gestão de usuários e tipos de mensagem.
-   Fluxo de primeiro acesso com troca obrigatória de senha.
-   Notificações visuais e feedback instantâneo ao usuário público.

## Arquitetura e Tecnologias

| Camada       | Tecnologia                 | Propósito                                    |
| ------------ | -------------------------- | -------------------------------------------- |
| Backend      | Laravel 11 (PHP 8.2)       | API REST e camada de serviço                 |
| Frontend     | Vue 3 + Inertia.js         | Interface reativa single-page                |
| Build        | Vite                       | Empacotamento e hot reload                   |
| Banco local  | SQLite                     | Persistência simples durante desenvolvimento |
| Estilo       | Tailwind CSS               | Estilização utilitária                       |
| Autenticação | Laravel Breeze customizado | Login seguro, proteção CSRF                  |

## Fluxo da Aplicação

1. **Usuário público** acessa a tela `Enviar Manifestação`, escolhe o tipo, descreve o fato e pode anexar arquivos.
2. **Sistema** armazena a mensagem, associando tipo, anexos e carimbo de data/hora sem vincular identidade.
3. **Administrador** autenticado acessa o painel, visualiza as mensagens, filtra e consulta detalhes completos.
4. **Equipe responsável** registra tratativas externas (fora do sistema) e atualiza indicadores internos.

## Requisitos

-   PHP 8.2+ com extensões `pdo`, `pdo_sqlite`, `mbstring`, `openssl`, `json`.
-   Composer 2.x.
-   Node.js 20+ e npm.
-   SQLite 3.39+ (ou driver compatível em produção).
-   Docker Engine + Docker Compose (opcional).

## Configurando o Ambiente

### Instalação Local

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
```

### Configuração do Banco

```bash
php artisan migrate --seed
```

Os seeders criam:

-   Tipos padrão de mensagens (`elogio`, `sugestão`, `reclamação`, `denúncia`).
-   Usuário administrador inicial `admin@example.com` (senha `password`, exige troca).

### Execução dos Servidores

```bash
php artisan serve          # API e painel em http://localhost:8000
npm run dev                # Build frontend com hot reload
```

Configure serviços adicionais (e-mail, armazenamento S3, cache Redis) editando `.env`.

## Execução com Docker

```bash
docker compose up -d --build
```

Para encerrar e limpar o ambiente:

```bash
docker compose down
docker system prune -a --volumes -f
```

## Testes e Garantia de Qualidade

-   Testes automatizados: `php artisan test`
-   Linters e estática (quando configurados):
    -   `./vendor/bin/phpcs`
    -   `./vendor/bin/phpstan analyse`
    -   `npm run lint`
-   Execute antes de enviar Pull Requests ou releases.

## Estrutura do Projeto

```
app/Http/Controllers       # Controladores REST do Laravel
app/Models                 # Modelos Eloquent (Message, MessageType, User)
database/migrations        # Esquema do banco de dados
database/seeders           # Dados iniciais (usuários, tipos)
resources/js               # SPA em Vue 3 + Inertia.js
resources/views            # Layout Blade principal
public/                    # Assets públicos e ponto de entrada
tests/                     # Testes de unidade e integração
```

## Boas Práticas de Desenvolvimento

-   Utilize commits seguindo Conventional Commits (`feat:`, `fix:`, `chore:`).
-   Abra PRs pequenos, com descrição objetiva e checklist de testes executados.
-   Respeite `phpcs`/`eslint` e ajuste formatação antes de solicitar revisão.
-   Nunca inclua dados sensíveis em commits (`.env`, dumps de banco).

## Como Contribuir

1. Abra uma issue relatando bug ou sugerindo melhoria.
2. Faça fork e crie uma branch: `git checkout -b feature/minha-feature`.
3. Implemente e documente as alterações.
4. Execute testes e linters.
5. Envie PR descrevendo o contexto, mudanças, impactos e passos de validação.

## Perguntas Frequentes

-   **As mensagens são realmente anônimas?** Sim. Não há associação com usuário autenticado ou IP armazenado por padrão.
-   **É possível adicionar novos tipos de mensagens?** Sim. Admins podem gerenciar os tipos pelo painel.
-   **Posso integrar com outros canais (e-mail, Slack)?** Configure jobs/listeners no Laravel para disparar notificações conforme necessidade.
-   **O sistema suporta bancos relacionais diferentes?** Sim. Ajuste o driver e credenciais no `.env` para MySQL, PostgreSQL etc.

## Suporte

-   Equipe responsável: TI / RH Corporativo.
-   Contato: `contato@example.com`.
-   Alternativa: abra uma issue nas ferramentas internas ou neste repositório.

## Licença

Disponibilizado sob licença MIT. Consulte `LICENSE` para detalhes.

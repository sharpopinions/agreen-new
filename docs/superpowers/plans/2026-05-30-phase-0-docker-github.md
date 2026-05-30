# Phase 0: Docker + GitHub Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Налаштувати локальне середовище розробки з Docker Compose (5 сервісів) та підключити GitHub репозиторій з GitHub Actions.

**Architecture:** Проект живе в Docker-контейнерах на локальній машині. PHP-код монтується як volume — зміни в файлах одразу видно без перезапуску контейнерів. Nginx приймає HTTP-запити і передає PHP-файли до PHP-FPM через FastCGI протокол.

**Tech Stack:** Docker 28, Docker Compose v2, PHP 8.3-FPM, Nginx alpine, PostgreSQL 17, Redis 7, MeiliSearch, GitHub Actions, Makefile, gh CLI.

---

## Структура файлів цього плану

```
agreen-new/
├── .github/
│   └── workflows/
│       └── ci.yml              ← GitHub Actions pipeline (скелет)
├── docker/
│   ├── php/
│   │   └── Dockerfile          ← PHP 8.3-FPM образ з розширеннями
│   └── nginx/
│       └── default.conf        ← Nginx конфіг для Laravel
├── docker-compose.yml          ← Оркестрація 5 сервісів
├── Makefile                    ← Shortcut команди
└── .gitignore                  ← Виключення для Git
```

---

## Концепції які вивчаємо

### Що таке Docker і навіщо він потрібен
Без Docker кожен розробник налаштовує PHP, PostgreSQL, Redis на своєму Mac по-різному — виникають проблеми "у мене працює, у тебе ні". Docker створює ізольоване середовище яке однаково працює на будь-якому комп'ютері та на сервері.

### Dockerfile vs docker-compose.yml
- **Dockerfile** — рецепт створення одного образу (image). Описує: яку ОС взяти, які пакети встановити, яку команду запустити.
- **docker-compose.yml** — оркестрація кількох контейнерів. Описує: які сервіси запустити, як вони з'єднані між собою, які порти відкрити.

### FastCGI (чому Nginx + PHP-FPM, а не просто PHP)
Nginx — веб-сервер: приймає HTTP-запити, роздає статичні файли (CSS, JS, images) дуже швидко. PHP-FPM — менеджер PHP-процесів: виконує PHP-код. Вони спілкуються через FastCGI протокол через мережу всередині Docker.

---

## Task 1: Створення структури папок

**Files:**
- Create: `docker/php/Dockerfile`
- Create: `docker/nginx/default.conf`

- [ ] **Step 1.1: Створи папки проекту**

Відкрий термінал і виконай:
```bash
cd /Users/admin/Desktop/agreen-new
mkdir -p docker/php docker/nginx .github/workflows
```

Перевірка — структура має виглядати так:
```bash
find . -type d | sort
# ./docker
# ./docker/php
# ./docker/nginx
# ./.github
# ./.github/workflows
```

- [ ] **Step 1.2: Створи `docker/php/Dockerfile`**

```dockerfile
# Базовий образ: офіційний PHP 8.3 з вбудованим PHP-FPM
# PHP-FPM = FastCGI Process Manager — менеджер PHP процесів
FROM php:8.3-fpm

# apt-get = менеджер пакетів Debian (Linux всередині контейнера)
# Встановлюємо системні залежності:
#   git        — потрібен Composer для завантаження пакетів
#   curl       — для завантаження файлів
#   zip/unzip  — Composer розпаковує пакети
#   libpq-dev  — C-бібліотека для PostgreSQL (потрібна для PHP розширення)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Копіюємо Composer з офіційного образу в наш контейнер
# Це ефективніше ніж встановлювати Composer через curl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# WORKDIR = робоча директорія всередині контейнера
# Всі наступні команди виконуються звідси
WORKDIR /var/www/html
```

- [ ] **Step 1.3: Створи `docker/nginx/default.conf`**

```nginx
server {
    # Nginx слухає порт 80 всередині контейнера
    listen 80;

    # root = де знаходяться файли сайту
    # Laravel завжди має public/ як точку входу
    root /var/www/html/public;

    # Файл за замовчуванням при зверненні до директорії
    index index.php;

    # Логи — важливо для дебагу
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;

    location / {
        # try_files: спочатку шукай файл ($uri),
        # потім директорію ($uri/),
        # якщо нічого — передай в index.php (Laravel router)
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        # Передаємо PHP файли до PHP-FPM контейнера
        # 'app' = назва сервісу в docker-compose (Docker DNS резолвить автоматично)
        # 9000 = стандартний порт PHP-FPM
        fastcgi_pass app:9000;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        fastcgi_read_timeout 300;
    }

    # Захист: забороняємо доступ до прихованих файлів (.env, .git)
    location ~ /\. {
        deny all;
    }
}
```

---

## Task 2: docker-compose.yml

**Files:**
- Create: `docker-compose.yml`

- [ ] **Step 2.1: Створи `docker-compose.yml`**

```yaml
# Оркестрація 5 сервісів: app, nginx, postgres, redis, meilisearch

services:

  # ── PHP-FPM (Laravel backend) ─────────────────────────────────────
  app:
    build:
      context: .           # контекст збірки = поточна папка
      dockerfile: docker/php/Dockerfile
    container_name: agreen-app
    volumes:
      # Монтуємо поточну папку в контейнер
      # Зміни в коді на Mac одразу видно в контейнері — без перезапуску!
      - .:/var/www/html
      # Виключаємо vendor/ з монтування — він повинен бути всередині контейнера
      - /var/www/html/vendor
    depends_on:
      postgres:
        condition: service_healthy   # чекає поки postgres готовий приймати з'єднання
      redis:
        condition: service_started
    networks:
      - agreen

  # ── Nginx (веб-сервер) ────────────────────────────────────────────
  nginx:
    image: nginx:alpine              # готовий образ з Docker Hub, не будуємо свій
    container_name: agreen-nginx
    ports:
      - "8080:80"                    # Mac:8080 → контейнер:80
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - agreen

  # ── PostgreSQL (база даних) ───────────────────────────────────────
  postgres:
    image: postgres:17-alpine
    container_name: agreen-postgres
    environment:
      POSTGRES_DB: agreen
      POSTGRES_USER: agreen
      POSTGRES_PASSWORD: secret
    volumes:
      # Named volume — дані БД зберігаються між перезапусками контейнера
      - postgres_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"                  # доступ з Mac (TablePlus, DBeaver)
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U agreen"]
      interval: 5s
      timeout: 5s
      retries: 5
    networks:
      - agreen

  # ── Redis (кеш + черги) ───────────────────────────────────────────
  redis:
    image: redis:7-alpine
    container_name: agreen-redis
    ports:
      - "6379:6379"
    networks:
      - agreen

  # ── MeiliSearch (пошук) ───────────────────────────────────────────
  meilisearch:
    image: getmeili/meilisearch:v1.6
    container_name: agreen-meilisearch
    environment:
      MEILI_MASTER_KEY: "masterkey-local"
      MEILI_ENV: "development"
    ports:
      - "7700:7700"
    volumes:
      - meilisearch_data:/meili_data
    networks:
      - agreen

# ── Named volumes (дані зберігаються між docker compose down/up) ────
volumes:
  postgres_data:
  meilisearch_data:

# ── Внутрішня мережа для комунікації між сервісами ──────────────────
networks:
  agreen:
    driver: bridge
```

- [ ] **Step 2.2: Перша перевірка — спробуй збудувати образ**

```bash
cd /Users/admin/Desktop/agreen-new
docker compose build
```

Очікуваний результат (займає 1-3 хвилини першого разу):
```
[+] Building ...
 => [app] FROM docker.io/library/php:8.3-fpm
 => [app] RUN apt-get update ...
 => [app] COPY --from=composer:latest ...
 => [app] WORKDIR /var/www/html
 => FINISHED
```

Якщо є помилки — скоріш за все проблема з інтернетом або опечатка в Dockerfile.

---

## Task 3: Makefile

**Files:**
- Create: `Makefile`

- [ ] **Step 3.1: Створи `Makefile`**

Makefile — файл з короткими псевдонімами для довгих команд. Замість `docker compose exec app php artisan migrate` пишеш просто `make migrate`.

**Важливо:** відступи в Makefile — це **TAB**, не пробіли. Більшість редакторів підставляють пробіли, тому набирай TAB вручну або скопіюй як є.

```makefile
# ── Змінні ───────────────────────────────────────────────────────────
APP = docker compose exec app

# ── Docker ───────────────────────────────────────────────────────────

## Запустити всі контейнери у фоні
up:
	docker compose up -d

## Зупинити всі контейнери
down:
	docker compose down

## Перезбудувати образи і запустити
rebuild:
	docker compose down && docker compose build && docker compose up -d

## Показати статус контейнерів
ps:
	docker compose ps

## Показати логи (Ctrl+C щоб вийти)
logs:
	docker compose logs -f

## Логи конкретного сервісу: make logs-app / make logs-nginx
logs-%:
	docker compose logs -f $*

# ── Shell ─────────────────────────────────────────────────────────────

## Відкрити bash в PHP контейнері
shell:
	docker compose exec app bash

## Відкрити psql в PostgreSQL контейнері
psql:
	docker compose exec postgres psql -U agreen agreen

# ── Laravel ───────────────────────────────────────────────────────────

## Виконати міграції
migrate:
	$(APP) php artisan migrate

## Виконати міграції з очищенням БД (тільки для розробки!)
migrate-fresh:
	$(APP) php artisan migrate:fresh --seed

## Запустити PHP тести (Pest)
test:
	$(APP) php artisan test

## Запустити конкретний тест: make test-filter FILTER=LoginTest
test-filter:
	$(APP) php artisan test --filter=$(FILTER)

## Artisan команда: make artisan CMD="make:model Product"
artisan:
	$(APP) php artisan $(CMD)

## Composer команда: make composer CMD="require spatie/laravel-permission"
composer:
	$(APP) composer $(CMD)

# ── Node.js ───────────────────────────────────────────────────────────

## Запустити Vite dev сервер (hot reload)
dev:
	$(APP) npm run dev

## Зібрати assets для продакшну
build:
	$(APP) npm run build

## npm команда: make npm CMD="install vue"
npm:
	$(APP) npm $(CMD)

.PHONY: up down rebuild ps logs shell psql migrate migrate-fresh test artisan composer dev build npm
```

---

## Task 4: .gitignore

**Files:**
- Create: `.gitignore`

- [ ] **Step 4.1: Створи `.gitignore`**

```gitignore
# ── Laravel ───────────────────────────────────────────────────────────
/vendor/
/node_modules/
/public/hot
/public/build
/storage/*.key
.env
.env.*
!.env.example
*.cache

# ── Docker (локальне середовище — не для production) ─────────────────
docker-compose.override.yml

# ── PHPUnit / Pest ────────────────────────────────────────────────────
/coverage/
.phpunit.result.cache
/.phpunit.cache

# ── IDE ───────────────────────────────────────────────────────────────
.idea/
.vscode/
*.swp
*.swo
.DS_Store
Thumbs.db

# ── Logs ──────────────────────────────────────────────────────────────
/storage/logs/*.log
npm-debug.log
yarn-error.log
```

---

## Task 5: GitHub Actions skeleton

**Files:**
- Create: `.github/workflows/ci.yml`

- [ ] **Step 5.1: Створи `.github/workflows/ci.yml`**

Поки що пустий skeleton — заповнимо повністю в Phase 11. Але файл повинен бути валідним YAML щоб GitHub не скаржився.

```yaml
name: CI

# Тригери: запускається при push або PR в гілку main
on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  # Placeholder — заповнимо в Phase 11
  ci:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Placeholder
        run: echo "CI pipeline — буде налаштований в Phase 11"
```

---

## Task 6: Перший запуск Docker

- [ ] **Step 6.1: Запусти контейнери**

```bash
cd /Users/admin/Desktop/agreen-new
make up
```

Очікуваний результат:
```
[+] Running 5/5
 ✔ Container agreen-postgres     Started
 ✔ Container agreen-redis        Started
 ✔ Container agreen-meilisearch  Started
 ✔ Container agreen-app          Started
 ✔ Container agreen-nginx        Started
```

- [ ] **Step 6.2: Перевір що всі контейнери running**

```bash
make ps
```

Очікуваний результат (всі STATUS = running):
```
NAME                  IMAGE                      STATUS
agreen-app            agreen-new-app             Up X seconds
agreen-nginx          nginx:alpine               Up X seconds
agreen-postgres       postgres:17-alpine         Up X seconds (healthy)
agreen-redis          redis:7-alpine             Up X seconds
agreen-meilisearch    getmeili/meilisearch:v1.6  Up X seconds
```

- [ ] **Step 6.3: Перевір PHP версію**

```bash
docker compose exec app php -v
```

Очікуваний результат:
```
PHP 8.3.x (fpm-fcgi)
```

- [ ] **Step 6.4: Перевір PostgreSQL підключення**

```bash
make psql
```

Очікуваний результат — відкриється psql консоль:
```
psql (17.x)
agreen=#
```

Вийди: `\q` + Enter

- [ ] **Step 6.5: Перевір MeiliSearch**

```bash
curl http://localhost:7700/health
```

Очікуваний результат:
```json
{"status":"available"}
```

- [ ] **Step 6.6: Перевір nginx**

```bash
curl -I http://localhost:8080
```

Очікуваний результат (поки Laravel не встановлений, буде 404 від nginx — це нормально):
```
HTTP/1.1 404 Not Found
Server: nginx/...
```

Nginx відповідає — значить все підключено правильно. Laravel встановимо в Phase 1.

---

## Task 7: Git init та GitHub

- [ ] **Step 7.1: Ініціалізуй Git репозиторій**

```bash
cd /Users/admin/Desktop/agreen-new
git init
git branch -M main
```

Очікуваний результат:
```
Initialized empty Git repository in /Users/admin/Desktop/agreen-new/.git/
```

- [ ] **Step 7.2: Перший коміт**

```bash
git add .
git status
# Перевір що .env НЕ в списку (він у .gitignore)
git commit -m "chore: phase 0 — docker + github actions skeleton"
```

- [ ] **Step 7.3: Створи GitHub репозиторій через gh CLI**

```bash
gh repo create agreen --private --source=. --remote=origin --push
```

Пояснення флагів:
- `--private` — приватний репозиторій
- `--source=.` — поточна папка як джерело
- `--remote=origin` — додає remote з назвою origin
- `--push` — одразу пушить main гілку

Очікуваний результат:
```
✓ Created repository yourname/agreen on GitHub
✓ Added remote https://github.com/yourname/agreen
✓ Pushed commits to https://github.com/yourname/agreen
```

- [ ] **Step 7.4: Перевір що репо з'явилось на GitHub**

```bash
gh repo view --web
```

Відкриється браузер з GitHub репозиторієм.

- [ ] **Step 7.5: Перевір що GitHub Actions запустився**

```bash
gh run list
```

Очікуваний результат:
```
STATUS  TITLE                      WORKFLOW  BRANCH  EVENT   ID
✓       chore: phase 0 ...         CI        main    push    ...
```

---

## Task 8: Зупинка та повторний запуск (перевірка що все відтворюване)

- [ ] **Step 8.1: Зупини контейнери**

```bash
make down
```

- [ ] **Step 8.2: Запусти знову**

```bash
make up
make ps
```

Всі 5 контейнерів мають підніматись без помилок. Це підтверджує що конфігурація стабільна.

---

## Підсумок Phase 0

Після виконання цього плану маємо:

| Що | Де доступно |
|---|---|
| PHP 8.3-FPM | `docker compose exec app bash` |
| Nginx | `http://localhost:8080` |
| PostgreSQL | `localhost:5432` (TablePlus/DBeaver) |
| Redis | `localhost:6379` |
| MeiliSearch | `http://localhost:7700` |
| GitHub repo | `gh repo view --web` |
| GitHub Actions | запускається на кожен push |

Наступний крок — **Phase 1**: встановлення Laravel 12 всередині Docker контейнера.

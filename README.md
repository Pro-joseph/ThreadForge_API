# ThreadForge API

RESTful API that transforms raw developer notes into social media posts via Groq AI. Built with Laravel 13, Sanctum auth, async queue processing, and a Ghostwriter chat agent.

**Deployed API**: `http://20.243.176.179`

## Prerequisites

- Docker & Docker Compose
- Groq API key — [console.groq.com](https://console.groq.com) (free tier)

## Quick Start

```bash
# 1. Clone & configure
git clone <repo>
cd "ThreadForge API"
cp .env.example .env
```

Set your Groq API key in `.env`:
```
GROQ_API_KEY=gsk_your_key_here
AI_DEFAULT_PROVIDER=groq
```

```bash
# 2. Build & start
docker compose build --no-cache && docker compose up -d

# 3. Run migrations
docker exec threadforge_app php artisan migrate

# 4. Generate API docs (optional)
docker exec threadforge_app php artisan scribe:generate
```

## What's Running

| Container | Port | Purpose |
|---|---|---|---|
| `threadforge_app` | — | PHP-FPM (API) |
| `threadforge_nginx` | 8000 | Web server |
| `threadforge_queue` | — | Queue worker (Redis) |
| `threadforge_redis` | 6379 | Queue driver |

## API Endpoints

All authenticated endpoints require a Sanctum Bearer token passed as `Authorization: Bearer TOKEN`.

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/api/register` | No | Create account |
| POST | `/api/login` | No | Login, get token |
| POST | `/api/logout` | Yes | Revoke token |
| GET | `/api/blueprints` | Yes | List campaign blueprints |
| POST | `/api/blueprints` | Yes | Create blueprint |
| GET | `/api/blueprints/{id}` | Yes | Show blueprint |
| PUT | `/api/blueprints/{id}` | Yes | Update blueprint |
| DELETE | `/api/blueprints/{id}` | Yes | Delete blueprint |
| POST | `/api/content/repurpose` | Yes | Submit notes for AI processing |
| GET | `/api/posts` | Yes | List generated posts |
| GET | `/api/posts/{id}` | Yes | Show post |
| PUT | `/api/posts/{id}/status` | Yes | Update post status |
| POST | `/api/posts/{id}/chat` | Yes | Chat with Ghostwriter agent |

## Quick Test

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"a@b.com","password":"pass123","password_confirmation":"pass123"}'

# Use the returned token
TOKEN="1|abc123..."
curl http://localhost:8000/api/blueprints \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"

# Create a blueprint
curl -X POST http://localhost:8000/api/blueprints \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Tech Thought Leader","target_audience":"Developers","max_hashtags":5,"tone":"Professional","max_characters":280}'

# Submit content for AI processing
curl -X POST http://localhost:8000/api/content/repurpose \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"raw_content":"PHP 8.4 introduced property hooks.","campaign_blueprint_id":1}'

# View generated posts (after queue processes the job)
curl http://localhost:8000/api/posts \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## Tests

Tests use Pest with SQLite in-memory database. No external services needed.

```bash
# Run locally (no Docker required)
php artisan test

# Run inside a container
docker exec threadforge_app php artisan test
```

## Development Tools

| Tool | URL | Description |
|---|---|---|
| Clockwork | `http://localhost:8000/__clockwork/app` | Request profiling, DB queries, logs |
| API Docs | `http://localhost:8000/docs` | Scribe-generated interactive docs |
| WebGrind | `http://localhost:8080` | Xdebug profiler file viewer |

### Xdebug

- **Profiling**: Every request is profiled automatically. View results in Clockwork's Xdebug Profile tab or at WebGrind (`http://localhost:8080`).
- **Step-debugging**: Pass `XDEBUG_SESSION=VSCODE` cookie or query param to trigger. IDE listens on port 9003.

## Common Commands

```bash
# Restart queue after code changes
docker compose restart queue

# Run migrations
docker exec threadforge_app php artisan migrate

# Clear opcache on app or queue
docker exec threadforge_app php -r "opcache_reset();"
docker exec threadforge_queue php -r "opcache_reset();"

# View logs (Laravel Pail)
docker exec threadforge_app php artisan pail

# Access container shell
docker exec -it threadforge_app bash

# Rebuild containers after Dockerfile changes
docker compose build --no-cache && docker compose up -d
```

## Project Structure

```
app/
├── Agents/                  # GhostwriterAgent (chat agent)
├── Http/Controllers/Api/    # AuthController, BlueprintController, ContentController, PostController, ChatController
├── Http/Requests/           # Form Request validation classes
├── Http/Resources/          # API Resource transformers
├── Jobs/                    # RepurposeContentJob (async AI processing)
├── Models/                  # User, CampaignBlueprint, GeneratedPost
├── Policies/                # Authorization policies
└── Tools/                   # GetCampaignRules, GetPostHistory (agent tools)

docker/
├── nginx/default.conf       # Nginx server config
├── php/xdebug.ini           # Xdebug debug & profiler config
└── webgrind/Dockerfile      # WebGrind profile viewer

routes/api.php               # All API routes
docs/api-test-formats.md     # Reference: JSON request/response examples
```

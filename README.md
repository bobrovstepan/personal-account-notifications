# Personal Account Notifications

A Laravel 13 notification system featuring system and marketing notification types, delivery via database and email channels, queue-based processing, and a REST API for managing notifications.

## Requirements

- Docker + Docker Compose

## Getting Started

**1. Copy the environment file:**

```bash
cp .env.example .env
```

**2. Install PHP dependencies:**

```bash
docker compose --profile tools run --rm composer
```

**3. Start the application:**

```bash
docker compose up -d
```

This starts four services:
- `app` — PHP-FPM (Laravel)
- `nginx` — web server on port **8080**
- `mysql` — MySQL 8.4 on port **3307**
- `queue` — queue worker (`php artisan queue:work`)

On first boot the entrypoint automatically generates `APP_KEY`, waits for the database, and runs migrations.

The API is available at `http://localhost:8080/api`.

## API

All endpoints require authentication (`auth` middleware). Authenticate first to obtain a session or token, then include credentials with each request.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/notifications` | List notifications (supports `category`, `unread_only`, `page`, `per_page`) |
| `POST` | `/api/notifications` | Send a notification |
| `GET` | `/api/notifications/unread` | Unread count |
| `GET` | `/api/notifications/{id}` | Show a single notification |
| `PATCH` | `/api/notifications/{id}` | Mark a single notification as read |
| `PATCH` | `/api/notifications` | Mark all notifications as read |

**POST `/api/notifications` payload:**

```json
{
  "category": "system",
  "title": "Server Alert",
  "message": "CPU usage is critical.",
  "cta_url": null
}
```

`category` accepts `system` or `marketing`. `cta_url` is optional and only relevant for marketing notifications.

## Running Tests

```bash
docker exec pan_app php artisan test
```

## Code Quality

```bash
# Static analysis
docker exec pan_app ./vendor/bin/phpstan analyse --memory-limit=512M

# Code style check
docker exec pan_app ./vendor/bin/pint --test

# Auto-fix code style
docker exec pan_app ./vendor/bin/pint
```

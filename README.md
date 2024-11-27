# 📋Queue Message Project

## 🔧 Requirements
- Docker Engine
- Docker Compose
- Git

## 💻 Technology Stack
- PHP 8.1
- Laravel 10.x
- Nginx
- PostgreSQL 14
- Redis
- Laravel Horizon (Queue Management)

## 🚀 Installation Steps

### 1. Clone the Repository
```bash
git clone https://github.com/Bariskau/queue_message_project.git
cd queue_message_project
```

### Install it automatically or follow the steps below.
```bash
chmod +x setup.sh && ./setup.sh
```

### 2. Set Environment Variables
```bash
cp .env.example .env
```

### 3. Start Docker Containers
```bash
docker-compose up -d
```

### 4. Install Dependencies
```bash
docker-compose exec app composer install
```

### 5. Generate Application Key
```bash
docker-compose exec app php artisan key:generate
```

### 6. Run Database Migrations
```bash
docker-compose exec app php artisan migrate --seed
```

### 7. Process Messages
```bash
docker-compose exec app php artisan messages:process
```
This command fetches messages from the database, queues them into the system, and processes them. You can monitor the status of the messages using Laravel Horizon.

## 🧪 Testing
To run the project tests:
```bash
docker-compose exec app php artisan test
```

## 🌐 Accessible Services

| Service | URL |
|---------|-----|
| Application | http://localhost:8000 |
| Swagger Documentation | http://localhost:8000/api/documentation |
| Horizon Panel | http://localhost:8000/horizon |

## 📝 Notes
- All commands should be run in the project directory.
- Make sure Docker containers are running before accessing the services.
- If you encounter any issues, check the Docker logs.

---

### 🔔 Additional Note:
The `MESSAGE_WEBHOOK_URL` environment variable should be set to a URL provided by webhook.site. If the existing URL does not work, generate a new one from webhook.site and update the variable before trying again.

The webhook response should follow this format:

```json
{
  "message": "Accepted",
  "messageId": "67f2f8a8-ea58-4ed0-a6f9-ff217df4d849"
}
```

---

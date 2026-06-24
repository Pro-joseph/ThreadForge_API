# API Test Formats

## Register

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Your Name",
    "email": "you@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response** `201`:
```json
{
  "user": { "id": 1, "name": "Your Name", "email": "you@example.com" },
  "token": "1|abc123..."
}
```

---

## Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "you@example.com",
    "password": "password123"
  }'
```

**Response** `200`:
```json
{
  "user": { "id": 1, "name": "Your Name", "email": "you@example.com" },
  "token": "1|abc123..."
}
```

**Error** `422`:
```json
{
  "message": "The email field is required.",
  "errors": { "email": ["The email field is required."] }
}
```

---

## Blueprints

### Create

```bash
curl -X POST http://localhost:8000/api/blueprints \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Tech Thought Leader",
    "target_audience": "Software developers",
    "max_hashtags": 5,
    "tone": "Professional but approachable",
    "max_characters": 280
  }'
```

**Response** `201`:
```json
{
  "id": 1,
  "name": "Tech Thought Leader",
  "target_audience": "Software developers",
  "max_hashtags": 5,
  "tone": "Professional but approachable",
  "max_characters": 280,
  "created_at": "2026-06-23T15:00:00.000000Z",
  "updated_at": "2026-06-23T15:00:00.000000Z"
}
```

### List

```bash
curl http://localhost:8000/api/blueprints \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response** `200`:
```json
[
  {
    "id": 1,
    "name": "Tech Thought Leader",
    "target_audience": "Software developers",
    "max_hashtags": 5,
    "tone": "Professional but approachable",
    "max_characters": 280,
    "posts_count": 3,
    "created_at": "2026-06-23T15:00:00.000000Z",
    "updated_at": "2026-06-23T15:00:00.000000Z"
  }
]
```

### Show

```bash
curl http://localhost:8000/api/blueprints/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Update

```bash
curl -X PUT http://localhost:8000/api/blueprints/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Updated Blueprint",
    "tone": "More casual"
  }'
```

### Delete

```bash
curl -X DELETE http://localhost:8000/api/blueprints/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response** `204` (no body).

---

## Full Test Flow

```bash
# 1. Register
TOKEN=$(curl -s -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Test","email":"t@t.com","password":"p","password_confirmation":"p"}' \
  | python -c "import sys,json; print(json.load(sys.stdin)['token'])")

# 2. Create blueprint
BP_ID=$(curl -s -X POST http://localhost:8000/api/blueprints \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"BP","target_audience":"Devs","max_hashtags":3,"tone":"Casual","max_characters":280}' \
  | python -c "import sys,json; print(json.load(sys.stdin)['id'])")

# 3. Repurpose content
curl -s -X POST http://localhost:8000/api/content/repurpose \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"raw_content\":\"Your content here\",\"campaign_blueprint_id\":$BP_ID}"
```

# API Test Formats (JSON)

## Auth

### Register
```json
POST http://localhost:8000/api/register
Content-Type: application/json
Accept: application/json

{
  "name": "Your Name",
  "email": "you@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
→ `201`
```json
{
  "user": { "id": 1, "name": "Your Name", "email": "you@example.com" },
  "token": "1|abc123..."
}
```

### Login
```json
POST http://localhost:8000/api/login
Content-Type: application/json
Accept: application/json

{
  "email": "you@example.com",
  "password": "password123"
}
```
→ `200`
```json
{
  "user": { "id": 1, "name": "Your Name", "email": "you@example.com" },
  "token": "1|abc123..."
}
```

### Logout
```json
POST http://localhost:8000/api/logout
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `200`
```json
{ "message": "Logged out" }
```

---

## Blueprints

### Create
```json
POST http://localhost:8000/api/blueprints
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "name": "Tech Thought Leader",
  "target_audience": "Software developers",
  "max_hashtags": 5,
  "tone": "Professional but approachable",
  "max_characters": 280
}
```
→ `201`
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
```json
GET http://localhost:8000/api/blueprints
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `200`
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
```json
GET http://localhost:8000/api/blueprints/1
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `200` (same shape as create response)

### Update
```json
PUT http://localhost:8000/api/blueprints/1
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "name": "Updated Name",
  "tone": "More casual"
}
```
→ `200`

### Delete
```json
DELETE http://localhost:8000/api/blueprints/1
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `204` (no body)

---

## Content

### Repurpose
```json
POST http://localhost:8000/api/content/repurpose
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "raw_content": "PHP 8.4 introduced property hooks, letting you define computed properties with get/set logic inline.",
  "campaign_blueprint_id": 1
}
```
→ `202`
```json
{ "message": "Content submitted for processing" }
```

---

## Posts

### List
```json
GET http://localhost:8000/api/posts
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `200`
```json
{
  "data": [
    {
      "id": 1,
      "campaign_blueprint_id": 1,
      "raw_content": "PHP 8.4 introduced...",
      "hook_propose": "New in PHP 8.4: Property Hooks!",
      "body_points": ["Point 1", "Point 2"],
      "technical_readability_score": 7,
      "suggested_hashtags": ["#PHP84"],
      "tone_compliance_justification": "Professional.",
      "status": "draft",
      "created_at": "2026-06-23T15:00:00.000000Z",
      "updated_at": "2026-06-23T15:00:00.000000Z"
    }
  ],
  "meta": { "current_page": 1, "last_page": 1, "total": 1 }
}
```

### Show
```json
GET http://localhost:8000/api/posts/1
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```
→ `200` (same shape as data item above, no wrapping)

### Update status
```json
PUT http://localhost:8000/api/posts/1/status
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{ "status": "published" }
```
→ `200`

### Chat
```json
POST http://localhost:8000/api/posts/1/chat
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{ "message": "Can you make the hook more engaging?" }
```
→ `200`
```json
{
  "response": "Sure! How about: 'New in PHP 8.4: Property Hooks—Bye‑Bye Boilerplate!'",
  "conversation_id": "019f0378-c588-7030-a451-182b5e798e38"
}
```

### Chat (follow-up)
```json
POST http://localhost:8000/api/posts/1/chat
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json
Accept: application/json

{
  "message": "Check the campaign rules too",
  "conversation_id": "019f0378-c588-7030-a451-182b5e798e38"
}
```

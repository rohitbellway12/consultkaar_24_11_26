# Consultkaar - AI Consultation Assistant (GPT-6 Luna)
## Integration & API Documentation

This module enables a **5-minute free AI consultation** inside Consultkaar, powered by **GPT-6 Luna**, complete with admin management, clinical summary generation, and human expert/doctor recommendations.

---

### Admin Panel Management

Access from the left sidebar: **AI Assistant (GPT-6 Luna)**
1. **AI Settings (`/admin/ai-assistant/settings`)**:
   - Enable/Disable AI Assistant switch.
   - Configure **API Secret Key**, **API Base Endpoint URL**, and Model (`gpt-6-luna`).
   - Configure **Free Chat Duration** (Default: 5 Minutes).
   - Configure **Cooldown Restriction** (Default: 24 Hours).
   - Configure **Max Messages Limit** per session (Default: 30).
   - Configure **Number of Recommended Advisors** (Default: 3) and sorting criteria.
   - Customize the **System Instruction Prompt**.
2. **Trending Questions (`/admin/ai-assistant/questions`)**:
   - Add, edit, toggle status, and delete sample FAQ chips for the app's AI home screen.
3. **Chat History & Logs (`/admin/ai-assistant/sessions`)**:
   - View all user chat sessions, duration, status, clinical summaries, and full message transcripts.

---

### Mobile App API Endpoints

**Base Path:** `/api/v1/ai`

#### 1. Home Screen Data
* **Endpoint:** `GET /api/v1/ai/home`
* **Headers:** `Authorization: Bearer <token>` (optional, to check user cooldown)
* **Response:**
```json
{
  "status": "success",
  "code": 200,
  "data": {
    "ai_service_enabled": true,
    "model": "gpt-6-luna",
    "free_duration_minutes": 5,
    "cooldown_hours": 24,
    "can_start_chat": true,
    "cooldown_remaining_seconds": 0,
    "active_session": null,
    "categories": [
      { "id": 4, "name": "General Physician", "image": "..." }
    ],
    "trending_questions": [
      { "id": 1, "question": "What to do for severe headache?", "category_id": 4 }
    ]
  }
}
```

#### 2. Start 5-Minute Free Consultation
* **Endpoint:** `POST /api/v1/ai/start-chat`
* **Headers:** `Authorization: Bearer <token>` (required)
* **Body:** `category_id` (optional, integer)
* **Response:**
```json
{
  "status": "success",
  "code": 201,
  "message": "5-minute AI Consultation session started.",
  "data": {
    "session_uuid": "3a74ef4b-01a7-47b8-b118-2d88078f44d1",
    "category_id": 4,
    "category_name": "General Physician",
    "remaining_seconds": 300,
    "started_at": "2026-11-25T11:00:00Z",
    "expires_at": "2026-11-25T11:05:00Z",
    "welcome_message": "Hello! I am your AI Consultation Assistant powered by GPT-6 Luna..."
  }
}
```

#### 3. Send Message to AI
* **Endpoint:** `POST /api/v1/ai/send-message`
* **Headers:** `Authorization: Bearer <token>`
* **Body:**
  - `session_uuid`: string
  - `message`: string
* **Response (During 5 mins):**
```json
{
  "status": "success",
  "code": 200,
  "data": {
    "session_uuid": "3a74ef4b-01a7-47b8-b118-2d88078f44d1",
    "ai_reply": "Resting in a quiet room and staying hydrated can help...",
    "remaining_seconds": 240,
    "is_expired": false
  }
}
```
* **Response (When 5 mins expired):**
```json
{
  "status": "expired",
  "code": 200,
  "is_expired": true,
  "message": "Your 5-minute free consultation has ended...",
  "data": {
    "remaining_seconds": 0,
    "summary": "User reported mild headache and fever for 2 days...",
    "recommended_advisors": [
      {
        "id": 12,
        "name": "Dr. Sarah Khan",
        "rating": 4.9,
        "consultation_fee": 500,
        "image": "..."
      }
    ]
  }
}
```

#### 4. End Chat & Get Recommendations
* **Endpoint:** `POST /api/v1/ai/end-chat`
* **Body:** `session_uuid`

#### 5. Chat History
* **Endpoint:** `GET /api/v1/ai/history?page=1`

#### 6. Session Messages
* **Endpoint:** `GET /api/v1/ai/session-messages?session_uuid=<uuid>`

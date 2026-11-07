# REST API v1 Specification & Integration Guide
### Tailoring and Boutique Management SaaS

Author: **Imran Ahmed** ([https://imrandev.bd](https://imrandev.bd) | *me@imrandev.bd*)

---

## 1. Authentication

All requests to protected API endpoints require an `Authorization: Bearer <token>` header obtained via the login endpoint.

### `POST /api/v1/auth/login`
Obtain a Sanctum API token for staff, POS tablet, or external systems.

**Request Body:**
```json
{
  "email": "owner@tailor.test",
  "password": "Demo@12345!",
  "device_name": "ipad-cutting-counter"
}
```

**Success Response (`200 OK`):**
```json
{
  "message": "Authenticated successfully.",
  "access_token": "1|AbCdEf1234567890...",
  "token_type": "Bearer",
  "user": {
    "id": 2,
    "name": "Eleanor Vance (Boutique Owner)",
    "email": "owner@tailor.test",
    "role": "business_owner",
    "tenant_id": 1,
    "tenant_name": "Elegant Stitch Boutique",
    "branch_id": 1
  }
}
```

---

## 2. Customer Management

### `GET /api/v1/customers`
List paginated customers belonging to the authenticated tenant organization.

**Query Parameters:**
- `search` (string): Filter by first name, last name, phone, or customer code.
- `per_page` (int, default: 15): Number of items per page.

### `POST /api/v1/customers`
Register a new customer record.

**Request Body:**
```json
{
  "first_name": "Lord",
  "last_name": "Chesterfield",
  "phone": "+44 7911 887766",
  "email": "chesterfield@bespoke.co.uk",
  "gender": "male",
  "notes": "Prefers traditional double-breasted suit cuts."
}
```

---

## 3. Precision Measurement Engine

### `GET /api/v1/customers/{id}/measurements`
Retrieve all measurement profiles recorded for a customer.

### `POST /api/v1/customers/{id}/measurements`
Record a new measurement profile.

**Request Body:**
```json
{
  "template_id": 1,
  "profile_name": "Savile Row Slim Fit",
  "is_default": true,
  "posture": "Erect posture, slight right shoulder drop (0.5 inch)",
  "notes": "Roped shoulders, 3.5 inch peaked lapels.",
  "values": {
    "chest": 41.5,
    "waist": 34.0,
    "shoulder": 18.75,
    "sleeve_length": 25.5,
    "jacket_length": 30.5,
    "inseam": 32.5,
    "crotch_depth": 10.75,
    "bicep": 14.75
  }
}
```

---

## 4. Custom Tailoring Orders

### `POST /api/v1/orders`
Create a custom bespoke order. Automatically captures measurement profiles into frozen snapshots.

**Request Body:**
```json
{
  "customer_id": 1,
  "order_type": "bespoke_tailoring",
  "priority": "urgent",
  "due_date": "2026-11-20",
  "notes": "Client gala event on due date.",
  "items": [
    {
      "garment_type_id": 1,
      "item_name": "Two-Piece Bespoke Midnight Wool Suit",
      "measurement_profile_id": 1,
      "fabric_type_id": 1,
      "fabric_roll_id": 1,
      "fabric_source": "boutique_inventory",
      "fabric_length_used": 3.5,
      "unit_price": 1200.00,
      "quantity": 1,
      "special_instructions": "Milanese hand-stitched lapel buttonhole."
    }
  ]
}
```

### `POST /api/v1/orders/{id}/status`
Transition an order through its 18-stage workflow.

**Request Body:**
```json
{
  "status": "confirmed",
  "notes": "Advance deposit received. Job cards generated."
}
```

---

## 5. Hardware Biometric Attendance Sync

### `POST /api/v1/attendance/punch`
Bridge endpoint for biometric hardware terminals.

**Request Body:**
```json
{
  "user_id": 3,
  "type": "check_in",
  "device_uid": "ZK-TERMINAL-01",
  "verification_mode": "fingerprint",
  "timestamp": "2026-11-07 08:58:00"
}
```

**Success Response (`200 OK`):**
```json
{
  "message": "Successfully recorded check_in.",
  "record": {
    "tenant_id": 1,
    "user_id": 3,
    "attendance_date": "2026-11-07",
    "check_in": "08:58:00",
    "status": "present",
    "source": "biometric_terminal"
  }
}
```

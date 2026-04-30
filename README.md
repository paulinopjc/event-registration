# Event Registration Platform

A multi-event registration system built with CodeIgniter 4 and PostgreSQL. Organizers create events with ticket types and custom fields, attendees register through a public form, receive email confirmations with QR codes, and organizers manage attendee lists through an admin panel.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | CodeIgniter 4.7 |
| Language | PHP 8.3+ |
| Database | PostgreSQL 16 (Docker for local, Neon for production) |
| Auth | Google OAuth (whitelist-based, no self-registration) |
| Email | Brevo HTTP API (transactional emails with QR code attachments) |
| QR Codes | chillerlan/php-qrcode |
| Frontend | Bootstrap 5 (server-rendered views) |
| Testing | PHPUnit 10 |

## Features

- Create events with multiple ticket types (free or paid, with capacity limits)
- Custom registration fields per event (text, textarea, dropdown, checkbox, radio)
- Public event page with ticket selection and registration form
- QR code generation and email confirmation on registration
- Admin dashboard with event stats (total registrations, checked in)
- Attendee management: search, filter, check-in, cancel, resend confirmation
- CSV export of attendee lists
- REST API for check-in integration (scan QR code, mark as checked in)
- Event lifecycle: draft, published, closed

### Role-Based Access Control

Three user roles with different permission levels:

| Action | Admin | Editor | Viewer |
|--------|-------|--------|--------|
| View dashboard and events | Yes | Yes | Yes |
| Check in attendees | Yes | Yes | Yes |
| Export CSV | Yes | Yes | Yes |
| Create/edit events | Yes | Yes | No |
| Publish events | Yes | Yes | No |
| Resend confirmation emails | Yes | Yes | No |
| Close/archive events | Yes | No | No |
| Cancel registrations | Yes | No | No |
| Manage users | Yes | No | No |
| Manage guest lists | Yes | Yes | No |

### Restricted Events (Invite-Only)

Events can be marked as restricted for invite-only access:

- Restricted events are hidden from the public event listing
- Only accessible via direct link shared by the organizer
- Organizers upload a guest list (CSV: first_name, last_name, email)
- Registrants on the guest list are auto-confirmed with QR code and email
- Registrants not on the list are placed in "pending" status for admin approval
- Admins/editors can approve or reject pending registrations
- Guest list tracks who has registered and who hasn't

### Authentication

Google OAuth with whitelist-based access. No email/password auth, no public registration.

1. Admin user `paulinopjc@gmail.com` is seeded on first deploy
2. Admins add new users through the admin panel (name + email + role)
3. Users sign in with Google; backend verifies the ID token and checks the email against the users table
4. Only users in the database can sign in

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/registrations/:code | Look up registration by confirmation code |
| POST | /api/registrations/:code/checkin | Check in an attendee |
| GET | /api/events/:id/stats | Get event registration stats |

## Project Structure

```
event-registration/
  app/
    Controllers/
      Admin/              # DashboardController, EventController, AttendeeController,
                          # UserController, GuestListController
      Api/                # RegistrationApiController, EventApiController
      Auth/               # LoginController (Google OAuth)
      Public/             # RegisterController (public registration flow)
    Database/
      Migrations/         # Users, Events, TicketTypes, Registrations, CustomFields,
                          # GuestLists, role/status constraint updates
      Seeds/              # AdminSeeder (paulinopjc@gmail.com)
    Filters/              # AdminAuth (session-based auth guard with role checking)
    Models/               # Event, Registration, TicketType, User, CustomField,
                          # CustomFieldValue, GuestList
    Views/
      admin/              # Dashboard, events (list, create, detail, guests),
                          # users (list, create, edit), attendee detail
      auth/               # Google OAuth login page
      emails/             # HTML confirmation email template
      errors/             # 403, 404, and other error pages
      layouts/            # Admin layout (navbar, sidebar, flash messages)
      public/             # Event page, registration form, confirmation page
  tests/
    Api/                  # RegistrationApiTest
    Controllers/          # AdminAccessTest, PublicRegistrationTest, QrCodeTest
    Models/               # EventModelTest, RegistrationModelTest
  docker-compose.yml      # PostgreSQL 16 for local development
  Dockerfile.prod         # Production Docker image for Render
```

## Getting Started

### Prerequisites

- PHP 8.3+ with extensions: intl, pgsql, pdo_pgsql, mbstring, curl, gd
- Composer
- Docker Desktop
- Google Cloud Console account (for OAuth Client ID)

### Setup

```bash
git clone <repo-url>
cd event-registration

composer install

# Start PostgreSQL
docker compose up -d

# Copy environment file and edit with your credentials
cp env .env

# Run migrations and seed admin user
php spark migrate
php spark db:seed AdminSeeder

# Start the dev server
php spark serve --port 8085
```

Open `http://localhost:8085` in your browser.

### Environment Variables

| Variable | Description |
|----------|-------------|
| GOOGLE_CLIENT_ID | Google OAuth Client ID |
| BREVO_API_KEY | Brevo transactional email API key |
| EMAIL_FROM | Sender email address |
| EMAIL_FROM_NAME | Sender display name |

### Run Tests

```bash
# Create test database (one-time)
winpty docker exec -it event-registration-db-1 psql -U postgres -c "CREATE DATABASE event_registration_test;"

# Run tests
php vendor/bin/phpunit
```

### Google OAuth Setup

1. Go to Google Cloud Console > Credentials
2. Create an OAuth Client ID (Web application)
3. Add `http://localhost:8085` to Authorized JavaScript origins
4. Copy the Client ID into `.env` as `GOOGLE_CLIENT_ID`

## Deployment

- **Database:** Neon PostgreSQL (free tier)
- **Application:** Render (Docker) via `Dockerfile.prod`
- **Email:** Brevo HTTP API (transactional emails, port 443)
- **Google OAuth:** Publish the app in Google Cloud Console and add production URL to authorized origins
- **Environment:** Set `BREVO_API_KEY`, `GOOGLE_CLIENT_ID`, `EMAIL_FROM`, `EMAIL_FROM_NAME` in Render dashboard

## License

MIT

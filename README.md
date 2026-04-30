# Event Registration Platform

A multi-event registration system built with CodeIgniter 4 and PostgreSQL. Organizers create events with ticket types and custom fields, attendees register through a public form, receive email confirmations with QR codes, and organizers manage attendee lists through an admin panel.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | CodeIgniter 4.7 |
| Language | PHP 8.3+ |
| Database | PostgreSQL 16 (Docker for local, Neon for production) |
| Auth | Google OAuth (whitelist-based, no self-registration) |
| Email | SMTP via Mailtrap (dev) / Postmark (production) |
| QR Codes | chillerlan/php-qrcode |
| Frontend | Bootstrap 5 (server-rendered views) |
| Testing | PHPUnit 10 (26 tests, 45 assertions) |

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
- Google OAuth login for admin (no password-based auth)

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
      Admin/              # DashboardController, EventController, AttendeeController
      Api/                # RegistrationApiController, EventApiController
      Auth/               # LoginController (Google OAuth)
      Public/             # RegisterController (public registration flow)
    Database/
      Migrations/         # Users, Events, TicketTypes, Registrations, CustomFields
      Seeds/              # AdminSeeder (paulinopjc@gmail.com)
    Filters/              # AdminAuth (session-based auth guard)
    Models/               # Event, Registration, TicketType, User, CustomField, CustomFieldValue
    Views/
      admin/              # Dashboard, event list, create event, event detail
      auth/               # Google OAuth login page
      emails/             # HTML confirmation email template
      layouts/            # Admin layout (navbar, sidebar, flash messages)
      public/             # Event page, registration form, confirmation page
  tests/
    Api/                  # RegistrationApiTest (6 tests)
    Controllers/          # AdminAccessTest (3 tests), PublicRegistrationTest (3 tests)
    Models/               # EventModelTest (4 tests), RegistrationModelTest (5 tests)
  docker-compose.yml      # PostgreSQL 16 for local development
```

## Getting Started

### Prerequisites

- PHP 8.3+ with extensions: intl, pgsql, pdo_pgsql, mbstring, curl, gd
- Composer
- Docker Desktop
- Google Cloud Console account (for OAuth Client ID)
- Mailtrap account (free, for testing emails)

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

### Run Tests

```bash
# Create test database (one-time)
winpty docker exec -it event-registration-db-1 psql -U postgres -c "CREATE DATABASE event_registration_test;"

# Run tests
php vendor/bin/phpunit
```

```
OK (26 tests, 45 assertions)
```

## Authentication

Google OAuth with whitelist-based access. No email/password auth, no public registration.

1. Admin user `paulinopjc@gmail.com` is seeded on first deploy
2. Admins add new users through the admin panel (name + email + role)
3. Users sign in with Google; backend verifies the ID token and checks the email against the users table
4. Only users in the database can sign in

### Google OAuth Setup

1. Go to Google Cloud Console > Credentials
2. Create an OAuth Client ID (Web application)
3. Add `http://localhost:8085` to Authorized JavaScript origins
4. Copy the Client ID into `.env` as `GOOGLE_CLIENT_ID`

## Deployment

- **Database:** Neon PostgreSQL (free tier)
- **Application:** Render (Docker) or VPS with Nginx + PHP-FPM
- **Google OAuth:** Publish the app in Google Cloud Console and add production URL to authorized origins

## License

MIT

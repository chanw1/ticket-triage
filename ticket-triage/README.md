# Ticket Triage Backend

This is the Laravel backend for Ticket Triage. It provides the REST API, manages tickets, and integrates with OpenAI for classification.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- OpenAI API Key

## Setup

1. Copy `.env.example` to `.env` and update database and OpenAI settings.
2. Install dependencies:
   composer install
3. Run migrations and seeders:
   php artisan migrate --seed
4. Serve the API:
   php artisan serve

## API Routes

- GET /api/tickets (list with filters, pagination)
- POST /api/tickets (create ticket)
- GET /api/tickets/{id} (show ticket)
- PATCH /api/tickets/{id} (update ticket)
- POST /api/tickets/{id}/classify (run AI classification)
- GET /api/stats (ticket statistics)
- GET /api/tickets/export (export CSV)


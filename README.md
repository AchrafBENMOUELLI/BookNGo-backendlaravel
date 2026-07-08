# BookNgo Backend

Laravel 10 REST API for hotel reservation management.

## Stack

- **Framework:** Laravel 10
- **Database:** PostgreSQL
- **Auth:** Laravel Sanctum (token-based)
- **Testing:** PHPUnit 10 (80 tests)
- **CI:** GitHub Actions

## Project Structure

```
app/
├── Http/
│   ├── Controllers/     # AuthController, AdminController, HotelController, ReservationController, FormuleTarifController, UserController
│   ├── Requests/        # Form request validation
│   └── Resources/       # API resource transformers
├── Models/              # Hotel, Reservation, User, FormuleTarif
├── Services/            # Business logic layer (HotelService, ReservationService, FormuleTarifService)
└── ...
database/
├── factories/           # Model factories
└── migrations/          # Schema
routes/
└── api.php              # API route definitions
tests/
├── Feature/             # Feature tests (Admin, Auth, Hotel, Reservation, FormuleTarif)
└── Unit/                # Unit tests (ModelRelations)
```

## API Routes

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | Public | Register user |
| POST | `/api/login` | Public | Login |
| GET | `/api/hotels` | Public | List hotels |
| GET | `/api/hotels/{id}` | Public | Get hotel |
| POST | `/api/logout` | Sanctum | Logout |
| GET | `/api/me` | Sanctum | Get profile |
| CRUD | `/api/formules-tarifs` | Sanctum | Manage formule tarifs |
| CRUD | `/api/reservations` | Sanctum | Manage reservations |
| GET | `/api/admin/stats` | Admin | Dashboard stats |
| CRUD | `/api/admin/hotels` | Admin | Manage hotels |
| CRUD | `/api/admin/users` | Admin | Manage users |
| GET/PATCH/DELETE | `/api/admin/reservations` | Admin | Manage all reservations |

## Getting Started

```bash
cp .env.example .env    # Configure DB credentials
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Tests

```bash
php artisan test                 # Run all tests
vendor/bin/phpunit               # Run all tests
vendor/bin/phpunit --coverage    # With coverage
```

## CI

GitHub Actions workflow runs tests on push/PR.

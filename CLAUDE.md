# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Laravel Commands
- `php artisan serve` - Start development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migration with seeders
- `php artisan tinker` - Interactive Laravel shell
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - View logs in real-time

### Testing
- `composer run test` - Run all tests (clears config first)
- `php artisan test` - Run PHPUnit tests directly
- Tests use Pest framework, located in `tests/` directory

### Development Workflow
- `composer run dev` - Start full development environment (server, queue, logs, vite)
- `npm run dev` - Start Vite development server for assets
- `npm run build` - Build production assets

### Code Quality
- Uses Laravel Pint for code formatting (available via `laravel/pint`)
- PHPUnit configuration in `phpunit.xml` with SQLite in-memory database for testing

## Architecture & Patterns

### Multi-Tenant Gym Management System
This is a Laravel + Filament application for gym management with multi-tenancy support.

### Core Models & Relationships
- **Gym**: Central tenant model
- **Client**: Belongs to gym, has many subscriptions and check-ins
- **Subscription**: Belongs to gym, many-to-many with clients
- **CheckIn**: Tracks gym visits, belongs to client and gym
- **User**: System users with role-based permissions

### Filament Structure
- **Resources**: CRUD interfaces in `app/Filament/Resources/`
- **Pages**: Custom pages in `app/Filament/Pages/`
- **Widgets**: Dashboard components in `app/Filament/Widgets/`
- **SuperAdmin Panel**: Separate admin panel at `app/Filament/SuperAdmin/`

### Key Traits & Patterns
- `BelongsToGym` trait: Ensures multi-tenant data isolation
- `HasPreferences` trait: User preference management
- `HasPagination` trait: Consistent pagination across resources
- UUID primary keys for all models
- Global search enabled for clients (name, card_id)

### Authentication & Authorization
- Filament Shield plugin for role/permission management
- Custom login page (`CustomLogin`)
- Policies for resource-level authorization
- Multi-gym tenant isolation

### Navigation Structure
- **Gestion** group: Client, Subscription, Membership, CheckIn resources
- **Configuración** group: Settings and configuration

### Livewire Components
- `Client/Search`: Client search functionality
- `CheckIn/Index`: Check-in management interface
- `Gym/Settings`: Gym-specific settings

### Database
- SQLite for development (`database/database.sqlite`)
- Factories available for all major models
- Seeders in `database/seeders/`

### Frontend Stack
- Tailwind CSS with forms and typography plugins
- Vite for asset compilation
- Alpine.js (via Filament)
- PostCSS with nesting support

## Important Implementation Notes

### Multi-Tenancy
- All gym-related models use `BelongsToGym` trait
- User context determines current gym via `getCurrentGymId()`
- Data isolation enforced at model level

### Client Management
- Card ID system for gym access
- Subscription status tracking with color-coded badges
- Automated check-in creation with locker assignment
- Global search functionality

### Resource Customization
- Custom infolists for detailed views
- Livewire integration for dynamic components
- Bulk actions available on list views
- Global search with custom result formatting
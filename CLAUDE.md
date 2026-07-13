# Claude Guidelines for CosmicLib Engine

This file guides Claude AI on how to interact with this repository and maintain the design architecture of **CosmicLib Engine**.

## Development Commands
When we begin the development phase (Fase 2+), use the following standard commands:
- **Composer Dependencies**: `composer install` or `composer update`
- **Artisan Commands**:
  - Run local dev server: `php artisan serve`
  - Run database migrations: `php artisan migrate`
  - Seed database: `php artisan db:seed`
  - Run automated tests: `php artisan test`
- **Vite Asset Compiler**:
  - Install npm packages: `npm install`
  - Run compiler in watch mode: `npm run dev`
  - Build assets for production: `npm run build`

## Code Style Guidelines
- **PHP Styling**: Follow PSR-12 formatting strictly.
- **Strict Typing**: Declare `declare(strict_types=1);` at the top of newly created service classes.
- **Naming Conventions**:
  - Classes and Controllers: PascalCase (e.g., `BookCirculationService`, `MemberController`).
  - Methods and Variables: camelCase (e.g., `borrowBook()`, `$memberId`).
  - Database Tables and Columns: snake_case (e.g., `borrow_records`, `return_date`).
  - Route names: kebab-case with dots as resource delimiters (e.g., `admin.books.index`).

## Project Focus
Currently, we are in the **Blueprint & Documentation Phase**. Do not scaffold or implement actual Laravel code until this phase is marked complete. Maintain clear, structured documentation inside the `docs/` folder.

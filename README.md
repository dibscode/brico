# Koperasi Bank Rakyat Indonesia (BRI Cooperative)

Aplikasi koperasi berbasis Laravel + Filament.

## Dokumentasi

- Project brief: [docs/project-brief.md](docs/project-brief.md)

## Menjalankan (Local)

1. Install dependencies:
	- `composer install`
	- `npm install`
2. Setup environment:
	- Salin `.env.example` → `.env`
	- `php artisan key:generate`
3. Setup database:
	- `php artisan migrate`
4. Jalankan aplikasi:
	- `npm run dev`
	- `php artisan serve`

## Admin Panel

- URL: `/admin`
- Buat akun admin via CLI:
  - `php artisan user:create-admin admin@brico.test --name="Admin BRICO" --password="password" --force`


# StuDyIo

StuDyIo is a no-login portfolio template generator for students and early-career creatives. Add your profile, education, skills, projects, experience and links, then preview it with a chosen portfolio layout.

## Technologies

- Laravel and PHP
- Blade templates
- Supabase PostgreSQL and Storage
- Vite, JavaScript and CSS
- Docker and Render are planned for production hosting

## Local setup

- Install the PHP dependencies with `composer install`.
- Install the frontend dependencies with `npm install`.
- Copy `.env.example` to `.env` and set the local application key and Supabase session pooler values.
- Build frontend assets with `npm run build`.
- Run database migrations and template seeds with `php artisan migrate --seed`.
- Start the app with `php artisan serve`.

Keep database passwords and the Supabase service role key in `.env`. The service role key is used only by the server for private Storage uploads and signed image links. Never commit `.env`.

## Database and hosting

Supabase is the database and file storage platform. Render is the planned hosting platform; the public URL and complete schema documentation will be added after deployment.

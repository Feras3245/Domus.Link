# Domus.Link
Click [here](https://github.com/Feras3245/Domus.Link/tree/main?tab=readme-ov-file#uiux-demo) to skip to UI/UX Demo section
## Overview

Domus.Link is a full stack real estate management platform developed during an internship as part of a technical assessment focused on PHP and Vue/Nuxt proficiency.

The application provides:

- Support for English and German
- User registration and authentication
- User and admin role management
- Light and dark themes
- Global navigation
- Admin tools for creating, editing, deleting, and toggling visibility of real estate listings
- A listing browser with pagination and advanced filtering
- Dedicated landing pages for individual properties

## Requirements, Dependencies, and Installation

### Requirements

- Node runtime v22+ and NPM
- PHP 8.2+
- Composer
- MariaDB
- Laravel installer (`composer global require laravel/installer`)
- Optional: Roadrunner server with the Laravel bridge for improved backend performance

### Dependencies

### Frontend

```json
"@nuxt/fonts": "^0.11.4",
"@nuxt/image": "^1.10.0",
"@nuxt/scripts": "^0.11.8",
"@nuxt/ui": "^3.1.3",
"@nuxtjs/i18n": "^9.5.5",
"@pinia/nuxt": "^0.11.2",
"@unhead/vue": "^2.0.10",
"@vueform/slider": "^2.1.10",
"cryptr": "^6.3.0",
"nuxt": "^3.17.5",
"nuxt-security": "^2.2.0",
"pinia": "^3.0.3",
"ufo": "^1.6.1",
"vue": "^3.5.16",
"vue-multiselect": "^3.2.0",
"vue-router": "^4.5.1",
"vue3-carousel-nuxt": "^1.1.6",
"zod": "^3.25.57"

```

### Backend

```json
"php": "^8.2",
"intervention/image": "^3.11",
"intervention/image-laravel": "^1.5",
"laravel/framework": "^12.0",
"laravel/sanctum": "^4.0",
"laravel/tinker": "^2.10.1",
"roadrunner-php/laravel-bridge": "^6.0",
"spiral/roadrunner-cli": "^2.7"

```

### Installation Steps

1. Update the `DB_USERNAME` and `DB_PASSWORD` values in `./DI-backend/.env` and adjust `DB_PORT` and `DB_HOST` if necessary.
2. In the `./DI-backend` directory, run `php artisan migrate:fresh --seed --seeder=ImmobilienSeeder`.
3. In `./DI-frontend`, run `npm install`.
4. Start the Nuxt development server with `npm run dev`.
5. In another terminal, navigate to `./DI-backend` and run `php artisan serve`.
6. Visit `http://localhost:3000` and create a new user.
7. To grant admin rights, manually update the user record in MariaDB:
    
    `UPDATE users SET role = 'ADMIN' WHERE email = 'email@example.com';`
    

## Architecture

The system follows a decoupled architecture with an independent Laravel backend serving as a dedicated API and a Nuxt application operating as the frontend layer.

A major design challenge was creating a unified authentication approach that maintains high security for the backend, supports simple session based authentication for the web frontend, and remains flexible enough for future mobile clients that rely on token based validation.

This was achieved using a Backend For Frontend pattern. A dedicated security layer in the Nuxt application acts as a controlled proxy responsible for handling session based authentication and injecting the appropriate token into all backend requests. This approach maintains strong session based security for web users while fully supporting token based authentication behind the scenes, ensuring both security and scalability for future platforms.

## Laravel API Routes

The backend provides `GET`, `POST`, `PATCH`, `PUT`, and `DELETE` routes. A full list is available in `./DI-backend/routes/api.php`. Key `GET` routes include:

- `api/register`: creates a new user
- `api/login`: authenticates a user
- `api/logout`: invalidates the session token
- `api/immobilien`: retrieves listings with support for filtering by price, rent, yield, area, usage type, object type, location list, sorting, order, and results per page
- `api/immobilien/{id}`: retrieves a specific listing
- `api/meta/immobilien`: provides min and max metadata for the filtering interface
- `api/meta/utypes`: returns available unit types

## Database Relational Diagram
<img src="https://github.com/Feras3245/Domus.Link/blob/main/database-diagram.png?raw=true" width="680">

## UI/UX Demo
Please wait if the GIFs are not loaded
### Login/Registration
![demo](https://github.com/Feras3245/Domus.Link/blob/main/demo-1.gif?raw=true)
### Navigation
![demo](https://github.com/Feras3245/Domus.Link/blob/main/demo-2.gif?raw=true)
### Real Estate Browser
![demo](https://github.com/Feras3245/Domus.Link/blob/main/demo-3.gif?raw=true)
### Landing Page
![demo](https://github.com/Feras3245/Domus.Link/blob/main/demo-4.gif?raw=true)


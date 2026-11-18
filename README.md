## Table of contents

- [About Crew Planning](#about-crew-planning)
- [Requirements](#requirements)
- [Setup](#setup)
- [Admin login](#admin-login)
- [Crew login](#crew-login)
- [Tests](#tests)

## About Crew Planning

Crew Planning is a basic application for managing employees, roles, and shifts. I built it to demonstrate how I work with Laravel routing and controllers, Inertia with Vue, usability, reusable Vue components, middleware, request validation classes, and tests.

This is a demonstration project and is not intended for production use.

## Requirements

- PHP 8.5 with the required extensions, including `pdo_mysql`.
- Composer 2.
- Node.js 22.13 or newer with npm.
- Bash.
- MySQL.

## Setup

1. Clone the repository and open its directory:

    ```bash
    cd crew-planning
    ```

2. Run the setup script:

    ```bash
    bash setup.sh
    ```

    The script creates `.env` from `.env.example` when needed, installs Composer and npm dependencies, generates an application key when empty, and builds the frontend assets. It preserves existing environment settings and application keys. Database configuration, creation, migrations, and seeding are left to the following steps.

3. Create an empty MySQL database named `crew_planning`, then configure `.env` with your connection details:

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=crew_planning
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

4. For a fresh database, create the tables and demo accounts:

    ```bash
    php artisan migrate --seed
    ```

5. Start the development servers:

    ```bash
    composer dev
    ```

    Open [http://localhost:8000](http://localhost:8000). Keep the command running while using the application.

## Admin login

After seeding the database, use these credentials on the login page:

| Email | Password |
| --- | --- |
| admin@crew.com | admin |

You will be redirected to the admin dashboard, where you can manage employees, roles, and shifts. The seed data contains users and roles; create shifts as an admin to populate the crew calendars.

## Crew login

Log out of the admin account and sign in with one of these demo accounts:

| Email | Password | Role |
| --- | --- | --- |
| cman@crew.com | test | Catering |
| cwoman@crew.com | test | Catering |
| shiftleader@example.com | test | Shift Leader |

You will be redirected to the crew dashboard. **My Shifts** shows assigned shifts. **Find shifts** shows shifts matching your roles, with space available and no overlap with your assigned shifts. Open a shift to view its details and accept it with confirmation.

## Tests

Tests use the separate MySQL database `crew_planning_testing`, configured in `phpunit.xml`. Create that database, copy `.env` to `.env.testing`, and set your MySQL connection details there. Then run:

```bash
composer test
```

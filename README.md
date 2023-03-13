# Getting started
 [![deploy to test instance](https://github.com/palladiumkenya/ushauri_dashboard/actions/workflows/cicd_process.yml/badge.svg)](https://github.com/palladiumkenya/ushauri_dashboard/actions/workflows/cicd_process.yml)
## Installation
<hr>
Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/8.x/installation)

Clone the repository

    git clone https://github.com/palladiumkenya/ushauri_dashboard

Switch to the repo folder
    cd ushauri_dashboard

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/palladiumkenya/ushauri_dashboard
    cd ushauri_dashboard
    composer install
    cp .env.example .env
    php artisan serve
    
 ----------

# Code overview

## Folders

- `app/Models` - Contains all the Eloquent models
- `app/Http/Controllers` - Contain all the data controllers
- `app/Http/Controllers/Auth` - Contains all the auth controllers
- `app/Http/Middleware` - Contains the JWT auth middleware
- `app/Http/Jobs` - Contains all the jobs handlers
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/seeds` - Contains the database seeder
- `resources` - Contains all the application views and styling files
- `routes` - Contains all the web routes defined in web.php file
- `tests` - Contains all the application tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------


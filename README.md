# About Project

## System Requirements
* PHP 8.2+
* Composer 2.7+
* Node.js v22+
* PHP Extensions `ext-pdo`, `pdo_sqlite`,`ext_json`

## Installation
Running the following commands will install the project. Always ensure that you have the required system requirements before running the commands and run commands sequentially in the root directory of the project.

```bash
git clone https://github.com/soysudhanshu/course-finder.git

cd course-finder

composer install

cp .env.example .env

php artisan key:generate

npm run build

npm install

```

## Seeding Database
```bash
php artisan db:seed
```

## Running the Project
```bash
php artisan serve
```

## Usage

### Creating API Tokens
API tokens are required to access the API endpoints which can modify data such as PUT, POST, DELETE methods. You can create API tokens by running the following command.

1. Register an user
```bash
php artisan user:create jhon jhon@gmail.com password
```

2. Create an API token using the email address
```bash
php artisan api:create-token jhon@gmail.com
```

### API Endpoints

```bash
GET /api/courses
GET /api/courses/{id}
POST /api/courses
PUT /api/courses/{id}
DELETE /api/courses/{id}
```

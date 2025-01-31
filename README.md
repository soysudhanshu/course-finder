# About Project

## System Requirements
* PHP 8.2+
* Composer 2.7+
* Node.js v22+
* PHP Extensions `ext-pdo`, `pdo_sqlite`,`ext_json`


## Deliverables

| Feature | Implemented | Description |
|---------|-------------|-------------|
| Frontend | ✅ | Implemented using Blade, Tailwing Alpine & JQuery UI |
| Backend | ✅ | Implemented using Laravel |
| Backend - Course Collection API | ✅ | Lists & filters courses |
| Backend - Course Single API | ✅ | Fetches a single course |
| Backend - Course Creation API | ✅ | Creates a new course |
| Backend - Course Update API | ✅ | Updates an existing course |
| Backend - Course Deletion API | ✅ | Deletes a course |
| Filters | ✅ | Filters courses by name, description, certified, free courses, categories, difficulty, duration, price, rating, release date, format, popularity |
| Authentication | ✅ | Implemented |
| API Tokens | ✅ | Implemented |
| Seeding | ✅ | Seeded the database with dummy data |
| Documentation | ✅ | Added API documentation in README.md |
| Feature Tests | ✅ | Implemented |
| Integration Tests | ✅ | Implemented |


## Installation
Running the following commands will install the project. Always ensure that you have the required system requirements before running the commands and run commands sequentially in the root directory of the project.

```bash
git clone https://github.com/soysudhanshu/course-finder.git

cd course-finder

composer install

cp .env.example .env

php artisan key:generate

npm install

npm run build



```

## Seeding Database
```bash
php artisan migrate
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
Project provides endpoints to perform CRUD operations on courses.

#### Authentication
Some endpoints require authentication. You can pass the API token in the header as `Authorization: Bearer {api_token}`

#### Endpoints
##### 1. Course Collection

```http
GET /api/courses
```
Fetches list of all courses and provides parameters to filter the courses.

**Parameters**
| Name | Type | Description |
|------|------|-------------|
| `search` | `string` | Search courses by name or description |
| `certified` | `bool` | Limits results to accredited/certified courses |
| `free_courses_only` | `bool` | Limits results to free courses |
| `categories` | `int[]` | Limits results to specified course categories |
| `difficulty` | `int` | Limits results to specified course difficulty |
| `duration` | `range` | Limits results to specified course duration |
| `price_min` | `int` | Limits results to specified course with price equal to or higher than specified value|
| `price_max` | `int` | Limits results to specified course with price equal to or less than specified value |
| `rating` | `range` | Limits result to specified ratings available values  `MORE_THAN_4`, `MORE_THAN_3`, `MORE_THAN_2`, `BETWEEN_0_TO_2`|
| `release_date` | `range` | Limits results to specified course release date interval |
| `format` | `string` | Limits results to specified course format |
| `popularity` | `string` | Limits results to specified course popularity |

**Example Request**
```http
GET /api/courses?search=&price_min=0&price_max=10000&categories%5B%5D=2&difficulty%5B%5D=1&rating=MORE_THAN_4
```

##### 2. Course Resource

```http
GET /api/courses/{id}
```
Retrieves a single course by its ID.

##### 3. Course Creation

```http
POST /api/courses
```
Registers a new course in the database. Note that this endpoint requires authentication.

**Parameters**
| Name | Type | Description |
|------|------|-------------|
| `name` | `string` | Name of the course |
| `description` | `string` | Description of the course |
| `difficulty` | `int` | Difficulty level of the course |
| `duration` | `int` | Duration of the course |
| `price` | `int` | Price of the course |
| `format` | `string` | Format of the course |
| `popularity` | `string` | Popularity of the course |
| `rating` | `float` | Rating of the course |
| `categories` | `int[]` | Categories of the course |
| `is_certified` | `bool` | Is the course certified |
|  `instructor` | `string` | Instructor of the course |

**Example Request**
```http
POST /api/courses
{
    "name": "Course Name",
    "description": "Course Description",
    "difficulty": 1,
    "duration": 10,
    "price": 100,
    "format": "online",
    "popularity": "high",
    "rating": 4.5,
    "categories": [1, 2],
    "is_certified": true,
    "instructor": "Instructor Name"
}
```
##### 4. Course Update

```http
PUT /api/courses/{id}
```
Allows updating an existing course. Note that this endpoint requires authentication.

**Parameters**
Same as the course creation endpoint.

**Example Request**
```http
PUT /api/courses/1
{
    "name": "Course Name",
    "description": "Course Description",
    "difficulty": 1,
    "duration": 10,
    "price": 100,
    "format": "online",
    "popularity": "high",
    "rating": 4.5,
    "categories": [1, 2],
    "is_certified": true,
    "instructor": "Instructor Name"
}
```

##### 5. Course Deletion

```http
DELETE /api/courses/{id}
```
Deletes a course from the database. Note that this endpoint requires authentication.

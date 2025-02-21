# Time Management API

A Laravel-based REST API for managing users, projects, and timesheets. This API provides authentication using Laravel Passport with both client credentials and user token support.

## Features

- User Authentication (Login/Register/Logout)
- Client Credentials for Service-level Access
- User Token for Individual Access
- CRUD Operations for Users, Projects, and Timesheets
- Role-based Access Control
- API Resource Responses
- Request Validation

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Laravel 11.x

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd astudio-assesment-alihabib
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file and configure your database:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. Install Passport:
```bash
php artisan passport:install
```

## API Documentation

### Obtaining Access Tokens

#### Client Credentials Token
```
POST /oauth/token
Content-Type: application/json

{
    "grant_type": "client_credentials",
    "client_id": "your_client_id",
    "client_secret": "your_client_secret",
    "scope": "*"
}

Response: 200 OK
{
    "token_type": "Bearer",
    "expires_in": 31536000,
    "access_token": "eyJ0eXAiOiJKV1..."
}

Error Response: 401 Unauthorized
{
    "error": "invalid_client",
    "error_description": "Client authentication failed",
    "message": "Client authentication failed"
}
```

### Authentication Endpoints

#### Register
```
POST /api/register
Content-Type: application/json
X-Client-Secret: PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET

{
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "password": "string"
}

Response: 201 Created
{
    "message": "User registered successfully",
    "user": {
        "id": integer,
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "full_name": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    },
    "access_token": "string"
}

Error Response: 422 Unprocessable Entity
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email has already been taken."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}

Error Response: 401 Unauthorized
{
    "message": "Invalid client secret"
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
    "email": "string",
    "password": "string"
}

Response: 200 OK
{
    "message": "Login successful",
    "user": {
        "id": integer,
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "full_name": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    },
    "access_token": "string"
}

Error Response: 401 Unauthorized
{
    "message": "Invalid credentials.",
    "errors": {
        "email": ["The provided email is not registered."],
        "password": ["The provided password is incorrect."]
    }
}
```

### User Management Endpoints

#### List Users (Client Credentials Required)
```
GET /api/users
Authorization: Bearer {client-token}

Response: 200 OK
{
    "data": [
        {
            "id": integer,
            "first_name": "string",
            "last_name": "string",
            "email": "string",
            "full_name": "string",
            "created_at": "timestamp",
            "updated_at": "timestamp"
        }
    ],
    "links": {},
    "meta": {}
}

Error Response: 401 Unauthorized
{
    "message": "Unauthenticated."
}

Error Response: 403 Forbidden
{
    "message": "Invalid scope"
}
```

#### Get Current User (User Token Required)
```
GET /api/users/me
Authorization: Bearer {user-token}

Response: 200 OK
{
    "data": {
        "id": integer,
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "full_name": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}
```

#### Create User (Client Credentials Required)
```
POST /api/users
Authorization: Bearer {client-token}
Content-Type: application/json

{
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "password": "string"
}

Response: 201 Created
{
    "message": "User created successfully",
    "data": {
        "id": integer,
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "full_name": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}
```

#### Update User (Client Credentials Required)
```
PUT /api/users/{id}
Authorization: Bearer {client-token}
Content-Type: application/json

{
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "password": "string"
}

Response: 200 OK
{
    "message": "User updated successfully",
    "data": {
        "id": integer,
        "first_name": "string",
        "last_name": "string",
        "email": "string",
        "full_name": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}
```

#### Delete User (Client Credentials Required)
```
DELETE /api/users/{id}
Authorization: Bearer {client-token}

Response: 200 OK
{
    "message": "User deleted successfully"
}
```

### Attributes Management (Client Credentials Required)

#### List Attributes
```
GET /api/attributes
Authorization: Bearer {client-token}

Response: 200 OK
{
    "data": [
        {
            "id": 1,
            "name": "First Name",
            "key": "first_name",
            "type": "text",
            "options": null,
            "default_value": null,
            "description": "User's first name",
            "created_at": "timestamp",
            "updated_at": "timestamp"
        }
    ],
    "links": {
        "first": "http://example.com/api/attributes?page=1",
        "last": "http://example.com/api/attributes?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}

Error Response: 401 Unauthorized
{
    "message": "Unauthenticated."
}
```

#### Get Single Attribute
```
GET /api/attributes/{id}
Authorization: Bearer {client-token}

Response: 200 OK
{
    "data": {
        "id": 1,
        "name": "First Name",
        "key": "first_name",
        "type": "text",
        "options": null,
        "default_value": null,
        "description": "User's first name",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 404 Not Found
{
    "message": "No query results for model [App\\Models\\Attribute]."
}
```

#### Create Attribute
```
POST /api/attributes
Authorization: Bearer {client-token}
Content-Type: application/json

{
    "name": "First Name",
    "key": "first_name",
    "type": "text",
    "options": ["option1", "option2"],  // Required only for type="select"
    "default_value": "John",
    "description": "User's first name"
}

Response: 201 Created
{
    "data": {
        "id": 1,
        "name": "First Name",
        "key": "first_name",
        "type": "text",
        "options": null,
        "default_value": "John",
        "description": "User's first name",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 422 Unprocessable Entity
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "key": [
            "The key has already been taken."
        ],
        "type": [
            "The selected type is invalid."
        ]
    }
}
```

#### Update Attribute
```
PUT /api/attributes/{id}
Authorization: Bearer {client-token}
Content-Type: application/json

{
    "name": "Updated First Name",
    "key": "updated_first_name",
    "type": "text",
    "options": ["option1", "option2"],  // Required only for type="select"
    "default_value": "Jane",
    "description": "Updated description"
}

Response: 200 OK
{
    "data": {
        "id": 1,
        "name": "Updated First Name",
        "key": "updated_first_name",
        "type": "text",
        "options": null,
        "default_value": "Jane",
        "description": "Updated description",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 422 Unprocessable Entity
{
    "message": "The given data was invalid.",
    "errors": {
        "key": [
            "The key has already been taken."
        ]
    }
}
```

#### Delete Attribute
```
DELETE /api/attributes/{id}
Authorization: Bearer {client-token}

Response: 200 OK
{
    "message": "Attribute deleted successfully",
    "data": {
        "id": 1,
        "name": "First Name",
        "key": "first_name",
        "type": "text",
        "options": null,
        "default_value": null,
        "description": "User's first name",
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 404 Not Found
{
    "message": "No query results for model [App\\Models\\Attribute]."
}
```

#### Attribute Types
The following types are supported for attributes:
- `text`: For text/string input
- `date`: For date input
- `number`: For numeric input
- `select`: For dropdown selection (requires options array)

## Authentication Types

### Client Credentials (Service Token)
Used for service-level operations. To obtain a client credentials token:

1. Create a client:
```bash
php artisan passport:client --client
```

2. Request a token:
```bash
curl -X POST http://your-domain/oauth/token \
    -H "Content-Type: application/json" \
    -d '{
        "grant_type": "client_credentials",
        "client_id": "your_client_id",
        "client_secret": "your_client_secret",
        "scope": "*"
    }'
```

### User Token
Used for user-specific operations. Obtained by:
1. Registering via `/api/register` (requires `X-Client-Secret` header)
2. Logging in via `/api/login`

## Error Handling

The API returns appropriate HTTP status codes:

- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

Error responses follow this format:
```json
{
    "message": "Error message here",
    "errors": {
        "field": ["Error description"]
    }
}
```

## Testing

Run the test suite:
```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

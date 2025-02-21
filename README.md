# Time Management API

A Laravel-based REST API for managing users, projects, EAV attributes, and timesheets. This API provides authentication using Laravel Passport with both client credentials and user token support.

## Features

- User Authentication (Login/Register/Logout)
- Client Credentials for Service-level Access
- User Token for Individual Access
- CRUD Operations for Users, Projects, Attributes, and Timesheets
- Role-based Access Control
- API Resource Responses
- Request Validation
- Advanced Filtering System

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Laravel 11.x

## Installation

1. Clone the repository:
```bash
git clone https://github.com/aliHabib123/astudio-assesment-alihabib
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

4. Update .env file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Set up Passport and configure credentials:
```bash
# Make the setup script executable
chmod +x setup-passport.sh

# Run the setup script
./setup-passport.sh
```

8. Seed the database with sample data (optional):
```bash
php artisan db:seed
```

This will create:
- Default project statuses (New, In Progress, Completed, On Hold)
- Sample attributes (Department, Start Date, End Date, User Role)
- A test user (email: test@example.com, password: password)
- 10 sample projects with random attributes

9. Start the development server:
```bash
php artisan serve
```

## Postman Setup

### Update Collection Variables
After running the setup script (`setup-passport.sh`), you need to update your Postman collection variables:

1. Open your Postman collection
2. Go to the "Variables" tab
3. Find these variables and update them with values from your `.env` file:
   - `PASSPORT_PASSWORD_GRANT_CLIENT_ID`
   - `PASSPORT_PASSWORD_GRANT_CLIENT_SECRET`

These credentials are required for authentication endpoints to work properly. You can find these values in your `.env` file after running the `setup-passport.sh` script.

### Automated Token Handling
The Postman collection includes automated scripts for token management:

1. When calling `/oauth/token` (Password Grant):
   - The access token is automatically extracted from the response
   - Stored in the collection variable `access_token`
   - Used for subsequent authenticated requests

2. When calling `/api/login` (User Login):
   - The user token is automatically extracted from the response
   - Stored in the collection variable `access_token`
   - Used for subsequent authenticated requests

This automation means you don't need to manually copy and paste tokens between requests - just execute the authentication endpoints and the tokens will be automatically set for the entire collection.

## Verifying Installation

To verify that everything is set up correctly:

1. Check if the API is running:
```bash
curl http://localhost:8000/api/health
```

2. Try logging in with the test user:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

3. Get an OAuth client credentials token:
```bash
curl -X POST http://localhost:8000/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "client_credentials",
    "client_id": "'$PASSPORT_PASSWORD_GRANT_CLIENT_ID'",
    "client_secret": "'$PASSPORT_PASSWORD_GRANT_CLIENT_SECRET'",
    "scope": "*"
  }'
```

If all these requests work, your installation is complete and working correctly!

## API Endpoints

## Oauth

- `POST /oauth/token` - Get access token (requires client credentials)

### Authentication
- `POST /api/register` - Register a new user (requires oauth access token)
- `POST /api/login` - Login and get access token
- `POST /api/logout` - Logout (requires authentication)

### Users (requires authentication)
- `GET /api/users/me` - Get current user's profile
- `PUT /api/users/me` - Update current user's profile

### Users (requires client credentials)
- `GET /api/users` - List all users
- `POST /api/users` - Create a new user
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user

### Projects (requires authentication)
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create a new project
- `GET /api/projects/{id}` - Get project details
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

### Timesheets (requires authentication)
- `GET /api/timesheets` - List all timesheets
- `POST /api/timesheets` - Create a new timesheet entry
- `GET /api/timesheets/{id}` - Get timesheet details
- `PUT /api/timesheets/{id}` - Update timesheet
- `DELETE /api/timesheets/{id}` - Delete timesheet

### Attributes (requires client credentials)
- `GET /api/attributes` - List all attributes
- `POST /api/attributes` - Create a new attribute
- `GET /api/attributes/{id}` - Get attribute details
- `PUT /api/attributes/{id}` - Update attribute
- `DELETE /api/attributes/{id}` - Delete attribute

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

### Users Response Format
```json
{
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "full_name": "John Doe",
        "created_at": "2024-02-21T10:00:00.000000Z",
        "updated_at": "2024-02-21T10:00:00.000000Z"
    }
}
```

### Create/Update User Request
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "secure_password"  // Only for create or password update
}
```

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

#### Example Attribute Payloads

1. Select Type with Options
```json
POST /api/attributes
Authorization: Bearer {client-token}
Content-Type: application/json

{
    "name": "User Role",
    "key": "user_role",
    "type": "select",
    "options": ["admin", "manager", "user", "guest"],
    "default_value": "user",
    "description": "Role assigned to the user"
}
```

2. Date Type
```json
{
    "name": "Birth Date",
    "key": "birth_date",
    "type": "date",
    "default_value": "1990-01-01",
    "description": "User's date of birth"
}
```

3. Number Type
```json
{
    "name": "Age",
    "key": "age",
    "type": "number",
    "default_value": "18",
    "description": "User's age"
}
```

4. Text Type
```json
{
    "name": "Address",
    "key": "address",
    "type": "text",
    "default_value": "",
    "description": "User's residential address"
}
```

Note: The `options` field is only required and used when `type` is set to `select`. For other types, you can omit this field or set it to `null`.

#### Attribute Types
The following types are supported for attributes:
- `text`: For text/string input
- `date`: For date input
- `number`: For numeric input
- `select`: For dropdown selection (requires options array)

## Filtering

The API supports dynamic filtering on both regular database fields and Entity-Attribute-Value (EAV) attributes. Filters can be applied using query parameters:

```
GET /api/projects?filters[field:operator]=value
```

### Available Operators

- `eq` - Equals (default if no operator specified)
- `gt` - Greater than
- `lt` - Less than
- `gte` - Greater than or equal to
- `lte` - Less than or equal to
- `like` - Contains (case-insensitive)
- `not` - Not equal to

### Filterable Fields

#### Regular Database Fields
- `name` - Project name
- `status_id` - Project status
- `created_at` - Creation date
- `updated_at` - Last update date
- `deleted_at` - Deletion date (for soft deletes)

#### Dynamic EAV Attributes
Any attribute defined in the system can be used as a filter using its `key`. Common examples include:
- `department` - Department name
- `start_date` - Project start date
- `end_date` - Project end date
- `user_role` - User role in project
- `priority` - Project priority
- `client` - Client name
- `budget` - Project budget

### Usage Examples

1. Basic filtering:
```http
GET /api/projects?filters[name]=Project Alpha
GET /api/projects?filters[department]=Marketing
```

2. Using operators:
```http
GET /api/projects?filters[created_at:gt]=2024-01-01
GET /api/projects?filters[name:like]=Alpha
GET /api/projects?filters[department:not]=Sales
```

3. Multiple filters:
```http
GET /api/projects?filters[department]=Marketing&filters[start_date:gt]=2024-01-01
```

4. Date range filtering:
```http
GET /api/projects?filters[start_date:gte]=2024-01-01&filters[end_date:lte]=2024-12-31
```

### Implementation Details

The filtering system combines regular database queries and EAV pattern:
- Regular fields are filtered directly on the projects table
- EAV attributes are filtered through a subquery joining the attributes and attribute_values tables
- Attribute keys (not display names) are used for filtering to ensure consistency
- All filters are automatically validated against their defined attribute types

## Projects Management (User Token Required)

### Projects Response Format
```json
{
    "data": {
        "id": 1,
        "name": "Project Alpha",
        "status": "completed",
        "attributes": [
            {
                "id": 1,
                "attribute": {
                    "id": 1,
                    "name": "Department",
                    "key": "department"
                },
                "value": "Marketing"
            }
        ],
        "created_at": "2024-02-21T10:00:00.000000Z",
        "updated_at": "2024-02-21T10:00:00.000000Z"
    }
}
```

### Create/Update Project Request
```json
{
    "name": "Project Alpha",
    "status": "completed",
    "attributes": {
        "department": "Marketing",
        "start_date": "2024-02-21"
    }
}
```

#### List Projects
```
GET /api/projects
Authorization: Bearer {user-token}

Response: 200 OK
{
    "data": [
        {
            "id": 1,
            "name": "Project Alpha",
            "status": {
                "id": 1,
                "name": "In Progress",
                "slug": "in-progress",
                "color": "#FFA500",
                "order": 2
            },
            "users": [
                {
                    "id": 1,
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "john@example.com",
                    "full_name": "John Doe",
                    "created_at": "timestamp",
                    "updated_at": "timestamp"
                }
            ],
            "attribute_values": [
                {
                    "id": 1,
                    "attribute_id": 1,
                    "value": "2025-03-01",
                    "attribute": {
                        "id": 1,
                        "name": "Start Date",
                        "key": "start_date",
                        "type": "date"
                    }
                }
            ],
            "created_at": "timestamp",
            "updated_at": "timestamp"
        }
    ],
    "links": {
        "first": "http://example.com/api/projects?page=1",
        "last": "http://example.com/api/projects?page=1",
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

#### Get Single Project
```
GET /api/projects/{id}
Authorization: Bearer {user-token}

Response: 200 OK
{
    "data": {
        "id": 1,
        "name": "Project Alpha",
        "status": {
            "id": 1,
            "name": "In Progress",
            "slug": "in-progress",
            "color": "#FFA500",
            "order": 2
        },
        "users": [
            {
                "id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "email": "john@example.com",
                "full_name": "John Doe",
                "created_at": "timestamp",
                "updated_at": "timestamp"
            }
        ],
        "attribute_values": [
            {
                "id": 1,
                "attribute_id": 1,
                "value": "2025-03-01",
                "attribute": {
                    "id": 1,
                    "name": "Start Date",
                    "key": "start_date",
                    "type": "date"
                }
            }
        ],
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 403 Forbidden
{
    "message": "Unauthorized to view this project"
}

Error Response: 404 Not Found
{
    "message": "No query results for model [App\\Models\\Project]."
}
```

#### Create Project
```
POST /api/projects
Authorization: Bearer {user-token}
Content-Type: application/json

{
    "name": "New Project",
    "status": "in-progress",
    "user_ids": [1, 2],
    "attribute_values": [
        {
            "key": "start_date",
            "value": "2025-03-01"
        },
        {
            "key": "priority",
            "value": "high"
        },
        {
            "key": "budget",
            "value": "10000.50"
        },
        {
            "key": "description",
            "value": "Project description"
        }
    ]
}

Response: 201 Created
{
    "data": {
        "id": 1,
        "name": "New Project",
        "status": {
            "id": 1,
            "name": "In Progress",
            "slug": "in-progress",
            "color": "#FFA500",
            "order": 2
        },
        "users": [...],
        "attribute_values": [...],
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 422 Unprocessable Entity
{
    "errors": {
        "name": [
            "The name field is required."
        ],
        "status": [
            "The selected status is invalid."
        ],
        "attribute_values.0.value": [
            "The value must be a valid date."
        ],
        "attribute_values.1.value": [
            "The selected value is invalid."
        ]
    }
}
```

#### Update Project
```
PUT /api/projects/{id}
Authorization: Bearer {user-token}
Content-Type: application/json

{
    "name": "Updated Project",
    "status": "completed",
    "user_ids": [1, 2, 3],
    "attribute_values": [
        {
            "key": "completion_date",
            "value": "2025-04-01"
        }
    ]
}

Response: 200 OK
{
    "data": {
        "id": 1,
        "name": "Updated Project",
        "status": {
            "id": 2,
            "name": "Completed",
            "slug": "completed",
            "color": "#00FF00",
            "order": 3
        },
        "users": [...],
        "attribute_values": [...],
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 403 Forbidden
{
    "message": "Unauthorized to update this project"
}

Error Response: 422 Unprocessable Entity
{
    "errors": {
        "status": [
            "The selected status is invalid."
        ],
        "attribute_values.0.value": [
            "The value must be a valid date."
        ]
    }
}
```

#### Delete Project
```
DELETE /api/projects/{id}
Authorization: Bearer {user-token}

Response: 200 OK
{
    "message": "Project deleted successfully",
    "data": {
        "id": 1,
        "name": "Project Alpha",
        "status": {...},
        "users": [...],
        "attribute_values": [...],
        "created_at": "timestamp",
        "updated_at": "timestamp"
    }
}

Error Response: 403 Forbidden
{
    "message": "Unauthorized to delete this project"
}
```

#### Attribute Value Types and Validation
When creating or updating projects, attribute values are validated based on their type:

1. **Text Type**
   ```json
   {
       "key": "description",
       "value": "Any text value"
   }
   ```

2. **Number Type**
   ```json
   {
       "key": "budget",
       "value": "1000.50"  // Must be a valid number
   }
   ```

3. **Date Type**
   ```json
   {
       "key": "start_date",
       "value": "2025-02-21"  // Must be a valid date
   }
   ```

4. **Select Type**
   ```json
   {
       "key": "priority",
       "value": "high"  // Must be one of the predefined options
   }
   ```

Notes:
- The creating user is automatically added as a project member
- Users can only access projects they are members of
- All attribute values are validated against their defined type
- For select-type attributes, values must match one of the predefined options
- The current user cannot be removed from the project's user list

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

## Authentication Flow

The API uses a two-tier authentication system:

### 1. Client Credentials (Service Level)
Required for:
- User Registration (`POST /api/register`)
- User Management (`/api/users/*`)
- Attribute Management (`/api/attributes/*`)

To access these endpoints:
1. Obtain a client credentials token using your client ID and secret
2. Use the token in the Authorization header: `Bearer <token>`

### 2. User Token (Individual Level)
Required for:
- Project Management (`/api/projects/*`)
- Timesheet Management (`/api/timesheets/*`)
- User Profile Operations (`/api/users/me`)

To access these endpoints:
1. Login to obtain a user token
2. Use the token in the Authorization header: `Bearer <token>`

## Error Handling

The API provides consistent error responses:

```json
{
    "status": "error",
    "message": "Descriptive error message",
    "code": 4xx
}
```

Common error codes:
- 401: Unauthorized (invalid or missing token)
- 403: Forbidden (insufficient permissions)
- 404: Resource not found
- 422: Validation error
- 500: Server error

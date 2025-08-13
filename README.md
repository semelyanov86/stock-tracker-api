# Stock Tracker API

A REST API application built with Symfony 7.3 and PHP 8.4 for tracking stock market values using the AlphaVantage API service.

## Features

- **Modern Tech Stack**: PHP 8.4, Symfony 7.3, Doctrine ORM
- **JWT Authentication**: Secure user authentication and authorization
- **AlphaVantage Integration**: Real-time stock market data
- **Asynchronous Processing**: Email notifications via RabbitMQ
- **Clean Architecture**: Interfaces, DTOs, dependency injection, and mock services
- **Database Logging**: Complete history of user stock queries
- **Email Notifications**: Automatic email alerts for stock quotes


## Prerequisites

- PHP 8.4 or higher
- Composer
- MySQL 8.0+
- RabbitMQ
- Docker \& Docker Compose (optional, for local development)


## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/semelyanov86/stock-tracker-api.git
cd stock-tracker-api
```


### 2. Install Dependencies

```bash
composer install
```


### 3. Environment Configuration

Copy the environment file and configure your settings:

```bash
cp .env .env.local
```

Update the following environment variables in `.env.local`:

```env
# Database configuration
DATABASE_URL="mysql://username:password@127.0.0.1:3306/stock_tracker?serverVersion=8.0&charset=utf8mb4"

# AlphaVantage API configuration
ALPHA_VANTAGE_API_KEY=your-alpha-vantage-api-key

# JWT configuration
JWT_SECRET=your-jwt-secret-key

# RabbitMQ configuration
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages

# Email configuration (optional, for testing with MailHog)
MAILER_DSN=smtp://localhost:1025
```


### 4. Start Services (Using Docker)

```bash
docker-compose up -d
```

This will start:

- MySQL database on port 3306
- RabbitMQ on ports 5672 (AMQP) and 15672 (Management UI)
- MailHog on ports 1025 (SMTP) and 8025 (Web UI)


### 5. Database Setup

Create the database and run migrations:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```


### 6. Start the Application

```bash
# Using Symfony CLI
symfony serve

# Or using PHP built-in server
php bin/console server:run
```

The API will be available at `http://localhost:8000` (or the port shown in the console).

### 7. Start Message Consumer

In a separate terminal, start the message consumer for email processing:

```bash
task consume
```

## API Documentation

### Base URL

```
http://localhost:8000/api
```


### Authentication

This API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:

```
Authorization: Bearer your-jwt-token
```


### Endpoints

#### 1. User Registration

Register a new user account.

**Endpoint:** `POST /api/register`
**Content-Type:** `application/json`

**Request Body:**

```json
{
    "email": "user@example.com",
    "password": "your-secure-password"
}
```

**Response (201 Created):**

```json
{
    "message": "User created successfully",
    "user_id": 1
}
```

**Error Response (409 Conflict):**

```json
{
    "error": "User with this email already exists"
}
```


#### 2. User Login

Authenticate and receive a JWT token.

**Endpoint:** `POST /api/auth/login`
**Content-Type:** `application/json`

**Request Body:**

```json
{
    "email": "user@example.com",
    "password": "your-secure-password"
}
```

**Response (200 OK):**

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "email": "user@example.com"
    }
}
```

**Error Response (401 Unauthorized):**

```json
{
    "error": "Invalid credentials"
}
```


#### 3. Get Stock Quote

Retrieve current stock information for a given symbol.

**Endpoint:** `GET /api/stock?q={symbol}`
**Authorization:** Required (Bearer token)

**Parameters:**

- `q` (required): Stock symbol (e.g., IBM, AAPL, APPL.US)

**Example Request:**

```bash
curl -H "Authorization: Bearer your-jwt-token" \
     "http://localhost:8000/api/stock?q=IBM"
```

**Response (200 OK):**

```json
{
    "name": "International Business Machines",
    "symbol": "IBM",
    "open": 234.53,
    "high": 237.96,
    "low": 233.36,
    "close": 234.77
}
```

**Note:** This endpoint also triggers an asynchronous email notification to the user with the stock quote information.

#### 4. Get Stock Query History

Retrieve the history of all stock queries made by the authenticated user.

**Endpoint:** `GET /api/history`
**Authorization:** Required (Bearer token)

**Example Request:**

```bash
curl -H "Authorization: Bearer your-jwt-token" \
     "http://localhost:8000/api/history"
```

**Response (200 OK):**

```json
[
    {
        "date": "2025-08-13T19:48:30+00:00",
        "name": "International Business Machines",
        "symbol": "IBM",
        "open": 234.53,
        "high": 237.96,
        "low": 233.36,
        "close": 234.77
    },
    {
        "date": "2025-08-13T15:30:15+00:00",
        "name": "Apple Inc.",
        "symbol": "AAPL",
        "open": 150.00,
        "high": 152.30,
        "low": 148.75,
        "close": 151.20
    }
]
```


## Testing

### Using Mock Service

For development and testing purposes, you can use the mock stock service instead of the real AlphaVantage API. Edit `config/services.yaml`:

```yaml
App\Service\StockDataProviderInterface:
    alias: App\Service\MockStockService
```

The mock service returns predefined data for symbols: IBM, AAPL, and APPL.US.

### Running Tests

```bash
task test
```


## Development Tools

### RabbitMQ Management Interface

Access the RabbitMQ management interface at:

- URL: http://localhost:15672
- Username: guest
- Password: guest


### MailHog (Email Testing)

View sent emails during development at:

- URL: http://localhost:8025

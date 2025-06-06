# Shinkansen Challenge API Server

Modern PHP REST API server built with Slim Framework.

## Features

- RESTful API endpoints
- CORS support
- Environment configuration
- Logging with Monolog
- Health check endpoint
- Sample train data API

## API Endpoints

- `GET /` - Welcome message
- `GET /health` - Health check
- `GET /api/trains` - List of shinkansen trains

## Local Development

### Using PHP built-in server:
```bash
composer install
composer start
```

The server will be available at `http://localhost:8080`

### Using Docker:
See `../local_env/` directory for Docker setup.

## Environment Configuration

Copy `.env.example` to `.env` and modify as needed:
```bash
cp .env.example .env
```
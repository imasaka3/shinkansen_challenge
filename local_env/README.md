# Local Environment

Docker orchestration for the Shinkansen Challenge API server.

## Services

- **web**: PHP-FPM + Nginx web server (port 8080)
- **mysql**: MySQL 8.0 database (port 3306)
- **phpmyadmin**: Database management interface (port 8081)

## Usage

### Start all services:
```bash
docker-compose up -d
```

### View logs:
```bash
docker-compose logs -f
```

### Stop all services:
```bash
docker-compose down
```

### Remove volumes (database data):
```bash
docker-compose down -v
```

## Access

- API Server: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3306

## Database Connection

- Host: mysql (from containers) or localhost (from host)
- Database: shinkansen
- Username: shinkansen
- Password: shinkansen
- Root Password: root
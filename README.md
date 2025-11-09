# Post API

Modern post API built with Symfony 7.3 using DDD (Domain-Driven Design) architecture and CQRS.

## 🚀 Features

### Users
- **User Registration** - creating new accounts
- **Login** - JWT authentication
- **Profile Management** - editing user data
- **Account Deletion** - deleting own account
- **User List** - available for administrators

### Posts
- **Adding Posts** - anonymous and registered posts
- **Browsing Posts** - pagination and filtering
- **Content Validation** - length and format restrictions

## 🏗️ Architecture

The project uses **Domain-Driven Design (DDD)** with the following structure:

```
src/
├── User/                    # User domain
│   ├── Domain/             # Business logic
│   ├── Application/         # Use cases (CQRS)
│   ├── Infrastructure/      # External implementations
│   └── UI/                 # API controllers
├── Post/                     # Post domain
└── Shared/                 # Shared components
```

### Design Patterns
- **CQRS** - command and query separation
- **Repository Pattern** - data access abstraction
- **Value Objects** - business logic encapsulation
- **Command/Query Handlers** - operation handling

## 🛠️ Technologies

- **PHP 8.2+** - modern PHP with types
- **Symfony 7.3** - web framework
- **Doctrine ORM** - object-relational mapping
- **JWT Authentication** - token authentication
- **API Platform** - automatic API documentation generation
- **PostgreSQL** - database
- **PHPUnit** - unit and functional tests

## 📋 Requirements

- PHP 8.2 or newer
- Composer
- PostgreSQL 16+
- Docker (optional)

## 🚀 Installation

### 1. Clone repository
```bash
git clone <repository-url>
cd guestbook-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Environment configuration
```bash
cp .env.example .env
```

Edit the `.env` file and set:
```env
# Database
DATABASE_URL="postgresql://app:!ChangeMe!@localhost:5432/app?serverVersion=16&charset=utf8"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase_here

# Application
APP_SECRET=your_app_secret_here
```

### 4. Generate JWT keys
```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Database migrations
```bash
php bin/console doctrine:migrations:migrate
```

### 6. Start application
```bash
php bin/console server:start
```

## 🐳 Docker

The project includes Docker Compose configuration:

```bash
# Run with Docker
docker-compose up -d

# Migrations in container
docker-compose exec app php bin/console doctrine:migrations:migrate
```

## 📚 API Endpoints

### Authentication
```
POST /auth/register     # User registration
POST /auth/login        # Login
```

### Users
```
GET    /user           # User list (ROLE_ADMIN)
GET    /user/me        # My profile (ROLE_USER)
PUT    /user/me        # Profile update (ROLE_USER)
DELETE /user           # Account deletion (ROLE_USER)
```

### Posts
```
GET  /posts        # Post list
POST /posts        # Add post
```

## 🧪 Testing

### Running tests
```bash
# All tests
php bin/phpunit

# Unit tests
php bin/phpunit tests/Unit/

# Functional tests
php bin/phpunit tests/Feature/
```

### Code coverage
```bash
php bin/phpunit --coverage-html coverage/
```

## 📊 Test structure

```
tests/
├── Unit/              # Unit tests
│   └── Post/
├── Feature/            # Functional tests
│   └── User/
└── DataFixtures/       # Test data
```

## 🔧 Development tools

### Code Quality
- **PHPStan** - static code analysis
- **PHP CS Fixer** - code formatting
- **PHPUnit** - automated tests

### Debugging
- **Symfony Profiler** - application profiling
- **Doctrine Debug** - SQL query analysis

## 📝 Usage examples

### User registration
```bash
curl -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123"
  }'
```

### Adding post
```bash
curl -X POST http://localhost:8000/posts \
  -H "Content-Type: application/json" \
  -d '{
    "author": {
      "type": "anonymous",
      "displayName": "Guest"
    },
    "message": "Great website!"
  }'
```

## 🏛️ Domain architecture

### User Domain
- **Entity**: `User` - main user entity
- **Value Objects**: `Email`, `Password`, `UserId`
- **Repository**: `UserRepositoryInterface`
- **Commands**: `CreateUserCommand`, `UpdateUserCommand`, `DeleteUserCommand`

### Post Domain
- **Entity**: `Post` - post
- **Value Objects**: `AuthorInterface`, `GuestAuthor`, `RegisteredAuthor`
- **Commands**: `CreatePostCommand`

## 🔒 Security

- **JWT Authentication** - stateless tokens
- **Password Hashing** - secure password hashing
- **Input Validation** - input data validation
- **CORS** - cross-origin requests configuration
- **Role-based Access** - role-based access control

## 📈 Performance

- **Doctrine Query Optimization** - query optimization
- **Pagination** - result pagination
- **Caching** - static data caching
- **Database Indexing** - database indexes

## 🤝 Contributing

1. Fork the repository
2. Create a branch for new feature
3. Write tests for new code
4. Ensure all tests pass
5. Create Pull Request

## 📄 License

The project is licensed under proprietary license.

## 👥 Authors

- [Your development team]

## 📞 Support

For issues or questions, contact the development team.

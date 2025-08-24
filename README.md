# InfoSys3 PHP Framework

This is a fun project. Not for productiv use! My primary target was to get a better understanding of how symfony/laravel-like systems work.  
Some things don't work. For instance the frontend, espacially the twig-templates do not allways render in a usable way (error messages). I may be / may be not working on these problems in the future. As I said, it's a fun projekt.   

## AI usage
I also tried some AI to see if it speeds up my workflow. I used ChatGpt5 and cursor. My main development tool is phpStorm, but using cursor paralle I was able to speed up things a lot.
### chatGpt
If you ask chatGpt a lot of  questions the Model will forget the thingsit told you to do. This is very annoying and interferes with your over all workflow. Progress is reversed and code is not proberly integrated. 

#### pros
* chatGpts answers are realy good even with the free plan model.
* makes good suggestions of what to add next to the project

#### cons
* does not remember what it told you
* when asked a lot, the already solved suggestions reappear

### cursor.ai
cursor is a Vscode-Like AI development environment. Therefor it knows the complete project.  
  
#### pros  
* analyses the complete project. 
* makes detailed suggestion, that can manually reviewed and accepted

#### cons
* in this setting is no privacy available. There is no such thing like company secrets. Uff. 

## Conclusion
AI  was very helpful. Is it a good idea to post all your company code to a tech gigant? I don't think so. But in the end this decision has to be made by the company.

# Usage of InfoSys3

A modern PHP MVC framework with dependency injection, middleware pipeline, and Twig templating.

## Features

- **Modern PHP 8.1+** with strict typing
- **Dependency Injection Container** with auto-wiring
- **Middleware Pipeline** for cross-cutting concerns
- **Twig Templating** with CSRF protection
- **Repository Pattern** with soft deletes
- **Docker Setup** with MariaDB
- **CSRF Protection** and security measures


## Installation

1. Clone the repository
2. Run the setup script: `php setup.php`
3. Edit `.env` file with your configuration
4. Run `docker-compose up -d`
5. Install dependencies: `composer install`
6. Run database migrations: `mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/001_create_blog_posts_table.sql`

## Environment Configuration

The application uses a centralized configuration system. Copy `env.example` to `.env` and configure:

### Application Settings
- `APP_ENV`: Environment (dev/prod)
- `APP_NAME`: Application name
- `APP_DEBUG`: Enable debug mode

### Database Settings
- `DB_HOST`: Database host
- `DB_PORT`: Database port
- `DB_NAME`: Database name
- `DB_USER`: Database user
- `DB_PASS`: Database password

### Security Settings
- `CSRF_TOKEN_LIFETIME`: CSRF token lifetime in seconds

### Additional Settings
- `LOG_LEVEL`: Logging level
- `MAIL_*`: Email configuration
- `CACHE_*`: Cache configuration
- `SESSION_*`: Session configuration

## API Endpoints

### Home
| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| GET | `/` | Home page | - |

### Blog Posts
| Method | Endpoint | Description | Authentication |
|--------|----------|-------------|----------------|
| GET | `/blog` | List all blog posts | - |
| GET | `/blog/{id}` | Show specific blog post | - |
| GET | `/blog/create` | Show create form | Required |
| POST | `/blog/create` | Create new blog post | Required |
| GET | `/blog/{id}/edit` | Show edit form | Required |
| POST | `/blog/{id}/edit` | Update blog post | Required |
| POST | `/blog/{id}/delete` | Delete blog post | Required |

### User Management
| Method | Endpoint | Description | Authentication | Role Required |
|--------|----------|-------------|----------------|---------------|
| GET | `/login` | Show login form | - | - |
| POST | `/login` | Authenticate user | - | - |
| GET | `/logout` | Logout user | Required | - |
| GET | `/profile` | Show user profile | Required | - |
| POST | `/profile` | Update password | Required | - |
| GET | `/users` | List all users | Required | Admin |
| GET | `/users/create` | Show create user form | Required | Admin |
| POST | `/users/create` | Create new user | Required | Admin |
| GET | `/users/{id}` | Show user details | Required | Admin or own profile |
| GET | `/users/{id}/edit` | Show edit user form | Required | Admin or own profile |
| POST | `/users/{id}/edit` | Update user | Required | Admin or own profile |
| POST | `/users/{id}/delete` | Delete user | Required | Admin |

### Default Login Credentials
- **Username:** `admin`
- **Password:** `admin123`
- **Role:** `admin`

### Request/Response Examples

#### Create Blog Post
```
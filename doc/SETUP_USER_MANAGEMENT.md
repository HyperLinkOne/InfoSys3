# User Management System Setup

This document provides step-by-step instructions to set up the user management system.

## Prerequisites

- PHP 8.1+
- MariaDB/MySQL
- Docker (optional)

## Installation Steps

### 1. Run Database Migrations

Execute the database migrations to create the required tables:

```bash
# Run the blog posts migration
mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/001_create_blog_posts_table.sql

# Run the users migration
mysql -h localhost -P 3306 -u dbuser -p infosys3 < database/migrations/002_create_users_table.sql
```

### 2. Verify Installation

After running the migrations, you should have:

- `blog_posts` table with blog functionality
- `users` table with user management and roles

### 3. Default Admin User

The migration automatically creates a default admin user:

- **Username:** `admin`
- **Password:** `admin123`
- **Email:** `admin@example.com`
- **Role:** `admin`

### 4. Access the Application

1. Start your application server
2. Visit `http://localhost:8080`
3. Click "Login" in the navigation
4. Use the default admin credentials to log in

## Features

### User Roles

- **Admin:** Full access to all features including user management
- **User:** Basic access to blog and profile management

### User Management Features

- ✅ User registration (admin only)
- ✅ User authentication
- ✅ Role-based access control
- ✅ Password management
- ✅ User profile management
- ✅ User deactivation
- ✅ Soft delete functionality
- ✅ Session management
- ✅ CSRF protection

### Security Features

- ✅ Password hashing with `password_hash()`
- ✅ Input validation and sanitization
- ✅ CSRF token protection
- ✅ Session-based authentication
- ✅ Role-based authorization
- ✅ SQL injection prevention

## Usage

### For Admins

1. **User Management:** Visit `/users` to manage all users
2. **Create Users:** Use `/users/create` to add new users
3. **Edit Users:** Modify user details and roles
4. **Delete Users:** Soft delete users (they can be restored)

### For Regular Users

1. **Profile Management:** Visit `/profile` to change password
2. **Blog Access:** Read and create blog posts
3. **Logout:** Use the logout link in the navigation

## API Endpoints

### Authentication
- `GET/POST /login` - User login
- `GET /logout` - User logout
- `GET/POST /profile` - User profile management

### User Management (Admin Only)
- `GET /users` - List all users
- `GET/POST /users/create` - Create new user
- `GET /users/{id}` - View user details
- `GET/POST /users/{id}/edit` - Edit user
- `POST /users/{id}/delete` - Delete user

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `.env`
   - Ensure MariaDB/MySQL is running

2. **Migration Errors**
   - Check database permissions
   - Verify table doesn't already exist

3. **Login Issues**
   - Verify default admin credentials
   - Check session configuration

4. **Permission Errors**
   - Ensure proper file permissions
   - Check web server configuration

### Debug Mode

Enable debug mode in your `.env` file:

```
APP_DEBUG=true
```

This will show detailed error messages for troubleshooting.

## Security Notes

- Change the default admin password immediately after installation
- Use strong passwords for all users
- Regularly backup your database
- Keep your application updated
- Monitor access logs for suspicious activity

## Support

For issues or questions:
1. Check the application logs
2. Verify database connectivity
3. Review the error messages in debug mode
4. Check the README.md for additional information

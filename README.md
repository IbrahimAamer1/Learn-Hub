
## 🎯 Overview

This is a full-featured e-learning platform built with Laravel 10 that supports multiple user roles (Admin, Instructor, Student) with comprehensive authentication and authorization mechanisms. The system allows instructors to create and manage courses, students to enroll in courses, and admins to oversee the entire platform.

## ✨ Features

### 🔐 Authentication & Authorization
- Multi-role user authentication (Admin, Instructor, Student)
- Email verification system
- Password reset functionality
- Role-based access control using Spatie Laravel Permission
- Protected routes with middleware

### 👨‍💼 Admin Panel
- Full CRUD operations for admins, users, roles, and categories
- Enrollment management (read-only)
- Instructor management and monitoring
- Profile management with password updates

### 👨‍🏫 Instructor Dashboard
- Course creation and management
- Lesson management for courses
- Student enrollment tracking
- Dashboard with statistics (total courses, students, enrollments)

### 👨‍🎓 Student Features
- Browse and search courses
- Course enrollment
- Access to course lessons
- Track lesson progress
- Leave reviews and ratings
- Profile management

### 📚 Course Management
- Category-based course organization
- Course details with descriptions
- Free and paid course options
- Lesson progression tracking
- Course reviews and ratings

## 🛠 Technology Stack

### Backend
- **PHP**: ^8.1
- **Laravel**: ^10.0
- **Laravel Sanctum**: ^3.2 (API authentication)
- **Spatie Laravel Permission**: ^6.23 (Role-based permissions)
- **Spatie Laravel Media Library**: ^11.17 (Media handling)
- **Spatie Laravel Sluggable**: ^3.7 (URL-friendly slugs)

### Frontend
- **Tailwind CSS**: ^3.1.0
- **Alpine.js**: ^3.4.2
- **Vite**: ^4.0.0 (Build tool)
- **Axios**: ^1.1.2 (HTTP client)

### Development Tools
- **Laravel Breeze**: ^1.29
- **Laravel Pint**: ^1.0 (Code style)
- **PHPUnit**: ^10.0 (Testing)

## 📦 Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Web server (Apache/Nginx) or PHP built-in server

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Authentication-and-Authorization
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Storage link**
   ```bash
   php artisan storage:link
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # Or for development:
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## ⚙️ Configuration

### Mail Configuration
Configure your mail settings in `.env` for email verification and password reset:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Permissions Setup
The application uses Spatie Laravel Permission. Permissions are seeded automatically. To manage permissions:
```bash
php artisan db:seed --class=PermissionSeeder
```

## 📁 Project Structure

```
Authentication-and-Authorization/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php          # Admin management
│   │   │   ├── RoleController.php           # Role management
│   │   │   ├── UserController.php           # User management
│   │   │   ├── Back/                        # Admin panel controllers
│   │   │   ├── Front/                       # Frontend controllers
│   │   │   └── Instructor/                  # Instructor dashboard controllers
│   │   ├── Middleware/
│   │   │   ├── Admin.php                    # Admin middleware
│   │   │   └── ...
│   │   └── Requests/                        # Form request validation
│   ├── Models/
│   │   ├── User.php                         # User model
│   │   ├── Admin.php                        # Admin model
│   │   ├── Course.php                       # Course model
│   │   ├── Lesson.php                       # Lesson model
│   │   ├── Enrollment.php                   # Enrollment model
│   │   ├── Review.php                       # Review model
│   │   └── ...
│   └── Notifications/                       # Email notifications
├── database/
│   ├── migrations/                          # Database migrations
│   └── seeders/                             # Database seeders
├── resources/
│   ├── views/
│   │   ├── back/                            # Admin panel views
│   │   ├── front/                           # Frontend views
│   │   └── instructor/                      # Instructor dashboard views
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php                              # Web routes
│   ├── adminAuth.php                        # Admin authentication routes
│   └── auth.php                             # User authentication routes
└── public/
    ├── assets-back/                         # Admin panel assets
    └── assets-front/                        # Frontend assets
```

## 👥 User Roles

### 🔴 Admin
- Full system access
- Manage all users, admins, and roles
- Manage categories
- View all enrollments and instructors
- Profile management

### 🟡 Instructor
- Create and manage own courses
- Create and manage lessons
- View enrolled students
- Track course statistics
- Profile management

### 🟢 Student
- Browse courses
- Enroll in courses
- Access course lessons
- Track lesson progress
- Leave reviews
- Profile management

## 🛣 API Routes

### Frontend Routes (`/front`)
- `GET /` - Home/Courses page
- `GET /front/courses` - Browse courses
- `GET /front/courses/{course}` - Course details
- `GET /front/courses/{course}/lessons/{lesson}` - Lesson view
- `POST /front/enrollments` - Enroll in course
- `GET /front/enrollments` - My enrollments
- `POST /front/lessons/{lesson}/mark-watched` - Mark lesson as watched
- `POST /front/courses/{course}/reviews` - Create review

### Admin Panel Routes (`/back`)
- `GET /back` - Admin dashboard
- Resource routes for admins, roles, users, categories
- `GET /back/enrollments` - All enrollments
- `GET /back/instructors` - All instructors
- Profile management routes

### Instructor Routes (`/instructor`)
- `GET /instructor/dashboard` - Instructor dashboard
- Resource routes for courses and lessons
- `GET /instructor/students` - Enrolled students

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific test suites:
```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## 📝 Key Features Implementation

### Authentication Flow
- Email verification required for new users
- Separate authentication for admin panel
- Password reset via email notifications

### Course Enrollment
- Students can enroll in courses
- Track enrollment status and progress
- Mark lessons as watched with progress tracking

### Authorization
- Middleware-based route protection
- Policy-based authorization for resources
- Role-based permission checks

## 🔒 Security Features

- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Password hashing (bcrypt)
- Email verification
- Rate limiting on authentication routes




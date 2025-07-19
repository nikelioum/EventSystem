# EventSystem – Laravel + Inertia.js Event Management System

This project is a submission for the **Backend Developer Technical Assessment**. It is a full-stack Laravel 12+ application built with Inertia.js and Vue 3, featuring an admin panel and RESTful API for managing events and registrations.

---

## 🔧 Tech Stack

- Laravel 12
- PHP 8.2
- MySQL
- Inertia.js
- Vue 3
- Tailwind CSS
- GitHub Actions (CI)

---

## 📦 Setup Instructions

```bash
# Clone the repository
git clone https://github.com/nikelioum/EventSystem.git
cd EventSystem

# Copy the example environment file
cp .env.example .env

--> Use mysql as DB

# Install PHP and JS dependencies
composer install
npm install

# Generate app key and run migrations
php artisan key:generate
php artisan migrate --seed

# Start the development servers
php artisan serve
npm run dev


# Run Test
php artisan test




Admin user info for loggin
Email: admin1@reborrn.com
Password: password

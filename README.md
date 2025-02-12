# ToxTrak - Appointment Management System

ToxTrak is a web-based appointment management system designed to help manage client appointments and track appointment details efficiently. The system features a clean, dark-themed interface and provides comprehensive appointment tracking capabilities.

## Author

Shakeel Khalid

## Features

- **User Authentication**
  - Secure login system
  - Session management
  - Protected routes

- **Client Management**
  - Add new clients
  - View client details
  - Edit client information
  - Delete clients
  - Search functionality

- **Appointment Management**
  - Create new appointments
  - View appointment details
  - Track appointment history
  - Search and filter appointments
  - Appointment reporting

## Technology Stack

- PHP
- MySQL
- Bootstrap 5.3.2
- Bootstrap Icons
- Custom CSS for styling

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server
- Composer (for dependency management)

## Installation

1. Clone the repository:
   ```bash
   git clone [your-repository-url]
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Configure your database:
   - Copy `.env.example` to `.env`
   - Update the `.env` file with your database credentials:
     ```
     DB_HOST=your_host
     DB_NAME=your_database_name
     DB_USER=your_username
     DB_PASS=your_password
     ```

4. Import the database schema (if provided)

5. Configure your web server to point to the project directory

## Project Structure

- `index.php` - Login page and entry point
- `dashboard.php` - Main dashboard after login
- `appointments.php` - Appointment management
- `clients.php` - Client management
- `report.php` - Reporting functionality
- `db.php` - Database connection configuration
- `style.css` & `dashboard.css` - Custom styling
- `vendor/` - Composer dependencies
- `data/` - Data storage directory

## Usage

1. Access the application through your web browser
2. Log in with your credentials
3. Navigate through the dashboard to:
   - Manage clients
   - Create and view appointments
   - Generate reports
   - Track appointment history

## Security Features

- Session-based authentication
- Password hashing
- SQL injection prevention
- Protected routes
- Environment variable usage for sensitive data

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## Support

For support, please contact:
- Email: shakeelkhalid786@gmail.com
- Phone: +923283070070

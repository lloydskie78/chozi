# ChoziPay - Secure Rental Payment System

## Overview

ChoziPay is a comprehensive payment processing system designed specifically for rental properties with an innovative broker commission system. The platform enables secure payments between tenants and property owners while allowing brokers to earn commissions through unique referral codes (ChoziCodes).

## 🚀 Key Features

### Core Payment System
- **Secure Payment Processing**: End-to-end encrypted payment transactions
- **Multi-Role Support**: Renters, Property Owners, and Brokers
- **Commission Calculation**: Automatic 5% broker commission system
- **ChoziCode Integration**: Unique referral codes for broker tracking

### Security Features (CRITICAL)
- ✅ **Input Validation & Sanitization**: XSS and SQL injection prevention
- ✅ **Authentication System**: Secure login with rate limiting
- ✅ **Password Security**: Bcrypt hashing with complexity requirements
- ✅ **CSRF Protection**: Laravel's built-in CSRF tokens
- ✅ **Security Headers**: CSP, XSS protection, clickjacking prevention
- ✅ **Transaction Logging**: Comprehensive audit trail
- ✅ **Suspicious Activity Detection**: Real-time security monitoring

### Technical Features
- **Backend**: Laravel 8.x with PHP 7.4+
- **Database**: MySQL with optimized schema
- **Frontend**: Bootstrap 5 + TypeScript integration
- **API**: RESTful endpoints with Sanctum authentication
- **Validation**: Server-side and client-side validation

## 🛠️ Installation & Setup

### Prerequisites
- PHP 7.4+ with extensions: mbstring, openssl, pdo, tokenizer, xml
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js 14+ and npm

### Step 1: Clone and Install
```bash
# Navigate to your web server directory (e.g., htdocs)
cd /path/to/your/webserver

# The project is already in chozipay/ directory
cd chozipay

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Compile assets
npm run dev
```

### Step 2: Environment Configuration
```bash
# Copy environment file (already exists)
# Update database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chozipay
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 3: Database Setup
```bash
# Create database (already done, but for reference)
mysql -u root -p -e "CREATE DATABASE chozipay;"

# Run migrations (already done)
php artisan migrate

# Generate application key (already done)
php artisan key:generate
```

### Step 4: Start Development Server
```bash
# Start Laravel development server
php artisan serve

# Or use your web server (Apache/Nginx) pointing to /public directory
```

## 📊 Database Schema

### Users Table
- Multi-role system (renter, owner, broker)
- Secure password hashing
- Wallet balance tracking
- Activity status monitoring

### Payments Table
- Payment processing with references
- Commission calculations
- Security metadata storage
- Status tracking (pending, completed, failed)

### ChoziCodes Table
- Unique broker referral codes
- Commission rate configuration
- Usage tracking and analytics
- Expiration date support

### Transactions Table
- Comprehensive audit logging
- Security event tracking
- Suspicious activity flagging
- User action monitoring

## 🔐 Security Implementation

### Authentication Security
```php
// Rate limiting: 5 attempts per IP
// Password requirements: 8+ chars, mixed case, numbers, special chars
// Session regeneration on login
// CSRF token validation
```

### Input Validation
```php
// XSS protection with htmlspecialchars()
// SQL injection prevention through Eloquent ORM
// Input sanitization middleware
// File upload restrictions
```

### Security Headers
```php
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
X-Content-Type-Options: nosniff
Strict-Transport-Security: max-age=31536000
Content-Security-Policy: [restrictive policy]
```

## 🌐 API Endpoints

### Authentication
```
POST /api/auth/login          - User login
POST /api/auth/logout         - User logout  
POST /api/auth/register       - User registration
```

### Payments
```
POST /api/payments            - Process payment
GET  /api/payments            - Get payment history
GET  /api/payments/stats      - Get payment statistics
GET  /api/payments/{ref}      - Get payment details
```

### ChoziCodes
```
POST /api/chozi-codes         - Generate ChoziCode (brokers only)
GET  /api/chozi-codes         - Get broker's ChoziCodes
GET  /api/chozi-codes/analytics - Get code analytics
POST /api/chozi-codes/validate  - Validate ChoziCode (public)
DEL  /api/chozi-codes/{id}    - Deactivate ChoziCode
```

### Users
```
GET  /api/user                - Get current user
GET  /api/users/search        - Search users for payments
```

## 💻 Frontend Features

### Modern UI/UX
- Responsive Bootstrap 5 design
- Interactive dashboards by user role
- Real-time notifications
- Progressive form validation

### TypeScript Integration
- Type-safe frontend development
- Enhanced IDE support
- Better error detection

### Security Features
- CSRF token inclusion
- XSS prevention in templates
- Secure form submissions
- Input validation feedback

## 🎯 User Roles & Workflows

### Renter Workflow
1. Register/Login to account
2. Search for property owner by email
3. Enter payment amount and details
4. Optional: Add ChoziCode for broker commission
5. Submit secure payment
6. Receive payment confirmation

### Property Owner Workflow
1. Register/Login as owner
2. View received payments dashboard
3. Track pending and completed payments
4. Monitor tenant payment history
5. Access detailed transaction reports

### Broker Workflow
1. Register/Login as broker
2. Generate unique ChoziCode
3. Share ChoziCode with potential clients
4. Track code usage and commissions
5. View analytics and earnings

## 🔍 Security Scenarios Addressed

### Scenario 1: Payment Amount Manipulation
- **Protection**: Server-side validation with type checking
- **Implementation**: Decimal validation, min/max limits
- **Logging**: All payment attempts logged with amounts

### Scenario 2: Secure User Authentication
- **Protection**: Rate limiting, secure sessions, password hashing
- **Implementation**: Bcrypt hashing, session regeneration, CSRF tokens
- **Monitoring**: Failed login attempts tracked and flagged

### Scenario 3: ChoziCode Validation
- **Protection**: Server-side code validation, expiration checking
- **Implementation**: Unique code generation, usage tracking
- **Security**: Broker ownership verification, active status validation

## 📈 Testing & Quality Assurance

### Security Testing
- Input validation testing
- Authentication bypass attempts
- CSRF token validation
- XSS payload testing
- SQL injection prevention

### Functionality Testing
- Payment processing workflows
- ChoziCode generation and usage
- User role permissions
- API endpoint responses

## 🚀 Deployment Considerations

### Production Setup
1. Set `APP_ENV=production` in .env
2. Configure proper database credentials
3. Set up SSL/HTTPS
4. Configure web server (Apache/Nginx)
5. Set proper file permissions
6. Enable error logging

### Performance Optimization
- Database indexing implemented
- Query optimization with Eloquent
- Asset compilation and minification
- Caching strategies for static content

## 📝 Development Notes

### Code Structure
- **Controllers**: RESTful API design
- **Models**: Rich domain models with relationships
- **Middleware**: Security and authentication layers
- **Migrations**: Version-controlled database schema
- **Views**: Blade templates with component reuse

### Security Best Practices
- No sensitive data in version control
- Environment-based configuration
- Secure default settings
- Regular dependency updates
- Comprehensive logging

## 🤝 Contributing

This project was developed as a technical assessment for ChoziPay. The codebase demonstrates:

- Secure payment processing implementation
- Role-based access control
- Comprehensive security measures
- Modern Laravel development practices
- RESTful API design
- Responsive frontend development

## 📞 Support & Documentation

For technical questions or security concerns, please refer to:
- Laravel Documentation: https://laravel.com/docs
- Security Best Practices: Implemented throughout the codebase
- API Documentation: Available at `/api/docs` endpoint

---

**Built with ❤️ for secure rental payment processing**

*ChoziPay Technical Assessment - Demonstrating secure payment system development with broker commission functionality.*

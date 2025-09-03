# AbstudERP

A comprehensive ERP system built with Laravel for educational institutions and student management.

## 🚀 Project Setup

Follow these steps to set up the project on your local environment:

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL or SQLite
- Git

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd AbstudERP
   ```

2. **Create environment file**
   ```bash
   cp .env.example .env
   ```
   
   Or on Windows:
   ```powershell
   Copy-Item .env.example .env
   ```

3. **Install PHP dependencies**
   ```bash
   composer install
   ```

4. **Install Node.js dependencies**
   ```bash
   npm install
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Configure your database**
   
   Edit your `.env` file and update the database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=abstud_erp
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Import location data (Countries, States, Cities)**
   ```bash
   php artisan location:import
   ```
   
   Available options:
   - `--countries` : Import only countries
   - `--states` : Import only states  
   - `--cities` : Import only cities
   - `--fresh` : Clear existing data before import
   - `--debug` : Enable debug mode

9. **Build frontend assets**
   ```bash
   npm run build
   ```
   
   For development with hot reload:
   ```bash
   npm run dev
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

## 🌍 Initial Configuration

### Domain and Country Setup

After completing the installation:

1. **Access the application**
   - Navigate to `http://localhost:8000` (or your configured domain)
   
2. **Configure Country Settings**
   - Go to the admin panel
   - Navigate to **Settings** → **Country Configuration**
   - Set up your primary country and regional settings
   - Configure timezone and currency preferences

## 📁 Project Structure

```
AbstudERP/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Mail/                # Mail classes
│   └── Services/            # Business logic services
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/            # Database seeders
│   └── data/               # JSON data files (countries, states, cities)
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
└── routes/                 # Application routes
```

## 🔧 Available Artisan Commands

- `php artisan location:import` - Import location data
- `php artisan serve` - Start development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migration with seeders

## 📊 Features

- **Student Management** - Comprehensive student records and enrollment
- **Lead Management** - Track and manage student leads
- **Location Management** - Countries, states, and cities data
- **Team Management** - User roles and permissions
- **Partner Management** - External partner relationships
- **Coaching Management** - Educational program management

## 🛠️ Development

### Running Tests
```bash
php artisan test
```

### Code Analysis
```bash
./vendor/bin/phpstan analyse
```

### Asset Compilation
For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## 🔒 Security

- Ensure your `.env` file is never committed to version control
- Configure proper file permissions for storage and bootstrap/cache directories
- Use HTTPS in production environments
- Regularly update dependencies

## 📝 Environment Variables

Key environment variables to configure:

```env
APP_NAME=AbstudERP
APP_ENV=local
APP_KEY=base64:generated_key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abstud_erp
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## 📄 License

This project is proprietary software. All rights reserved.

## 🆘 Support

For technical support or questions, please contact the development team.

pages_manage_metadata – App Functionality Description

1. User Authentication
     -> The user first logs into our CRM system using their system credentials.
     -> On the dashboard, a new feature “Connect with Facebook” is available.
     ->  When clicked, the user is redirected to Facebook Login to authorize our app.

2. Facebook Connection
    -> Once successfully connected, our system retrieves:
             User information
             Page list
             Access token
    -> The connected Facebook details and lead statistics are then visible on the CRM dashboard.

3. Page Subscription
    -> The user selects a Facebook Page from the retrieved list.
    -> By subscribing the page, our system connects to Facebook Webhooks for real-time lead retrieval.

4. Page Actions
    -> Two primary options are available for each subscribed page:
           1. Refresh Data – syncs page-related information (e.g., banner, category, metadata).
           2. Sync Forms – imports all lead forms associated with the selected page into the CRM.
    -> Once synced, the lead forms become available in the CRM with full details.

5. Lead Management
    -> Using the Facebook Lead Ads Testing Tool, a test lead can be generated.
    -> Leads can then be synced into the CRM with the “Sync Leads” option.
    -> Once synced, new leads appear in the system, and several triggers are executed automatically:
            1. Lead count is updated on the dashboard sidebar.
            2. Assigned users receive real-time notifications.
            3. Automated emails are sent to leads (e.g., confirmation email).
            4. Leads can also trigger WhatsApp messages and task assignments based on workflow rules.

6. Automation & Webhooks
   -> To provide instant response and avoid manual syncing, live Webhook functionality is required.
   -> This ensures that whenever a new lead is submitted on Facebook, it is automatically added to the CRM, and all associated triggers (notifications, emails, WhatsApp, assignments) are executed instantly.

Test Credentials

CRM URL: https://demo.teqcoder.com/
Username: test_user
Password: test12345

Facebook Account
email : nishant.abstud@gmail.com
Password: HCQCr_9ABtdK!jw
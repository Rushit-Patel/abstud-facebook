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


pages_manage_metadata:

1. User Login to our system with their system username & password.
2. Go to Dashboard Menu.
3. User then Click the button "Connect with Facebook" to import Facebook Account.
3. Our system get the user information, Page List & Access Token.
4. Then user can subscribe their pages to Webhook for facebook ads Leads.
5. There is two main option for Subscribed Page Sync Forms, Refresh Data. 
6. Refresh Data will sync all the required data for page like banner , facebook category & many more.
7. Sync Forms will sync all the page related lead forms to CRM.
8. when all forms are synced than view lead forms options got open in that you can see listing of currenty active lead forms . 
9. You can view leads for specific lead form where there is sync leads option is available.
10. After syncing leads new leads will add to the listing & triggers for new leads fire triggers are dynamic user can send email , whatsapp & that leads is going to assign to user that is specified where they get new notification that new lead has  been assigned to you where they can work on it.
11. for this functionality of triggers & instant response to user I need live webhook functionality so I can make this syncing process fully automated where user don't need to sync everytime they want to process like this. 


Test Credentials:

URL: https://demo.teqcoder.com/ 
Username: test_user
Password: test12345

Developer Facebook Account: nishant.abstud@gmail.com
Password : HCQCr_9ABtdK!jw
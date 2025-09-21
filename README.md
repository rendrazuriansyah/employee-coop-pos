# Household Goods and Equipment Procurement System for Employee Cooperatives

A comprehensive PHP-based Point of Sale (POS) system built with XAMPP 8.0 and Bootstrap 5, designed specifically for employee cooperatives managing household goods and equipment procurement.

## ✨ Latest Updates (September 2025)

### 🔧 Recent Fixes & Improvements
- **Sales Module**: Fixed missing `edit.php` file and resolved data loading issues for view/print functions
- **Reports Module**: Fixed "undefined array key 'nama_barang'" error in Top 10 Best Selling Items
- **Petugas Management**: Resolved "unknown column 'current_id'" error in staff edit functionality
- **Database Integrity**: Added missing transaction records for complete sales data (25 sales with full transaction details)
- **UI Consistency**: Enhanced Bootstrap 5 styling across all modules with professional gradients and responsive design

## 🚀 Features

### Core Functionality
- **User Authentication & Authorization**: Role-based access control (Admin, Manager, Kasir)
- **Customer Management**: Complete CRUD operations with search, pagination, and transaction history
- **Item/Product Management**: Inventory management with stock tracking, low-stock alerts, and profit calculations
- **Sales Management**: Full transaction processing with invoice generation, status management, and DO number tracking
- **Transaction Flow**: Multi-item transactions with temporary storage and stock validation
- **Reporting System**: Comprehensive analytics including sales trends, top customers, and inventory reports

### Technical Features
- **Responsive Design**: Bootstrap 5 UI consistent with modern design principles
- **Database Management**: MySQL with PDO for secure database operations
- **OOP Architecture**: Object-oriented programming with inheritance and polymorphism
- **Security**: Password hashing, SQL injection prevention, session management
- **Real-time Updates**: AJAX-powered dashboard statistics and notifications

## 📋 System Requirements

- **XAMPP 8.0** or higher
- **PHP 8.0** or higher
- **MySQL 5.7** or higher
- **Apache Web Server**
- **Modern Web Browser** (Chrome, Firefox, Safari, Edge)

## 🛠️ Installation Guide

### Step 1: Setup XAMPP
1. Download and install XAMPP 8.0 from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import the database structure:
   ```sql
   -- Navigate to the database folder and run pos_database.sql
   -- Or copy the contents of database/pos_database.sql and execute in phpMyAdmin
   ```

### Step 3: Application Setup
1. Clone or extract the project to your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\pos-app\
   ```
2. Ensure proper file permissions are set
3. Access the application via: `http://localhost/pos-app`

### Step 4: Default Login Credentials
- **Admin**: 
  - Username: `admin`
  - Password: `admin123`
- **Manager**: 
  - Username: `manager`
  - Password: `manager123`
- **Additional Staff**:
  - Kasir 1: `kasir1` / `kasir123`
  - Kasir 2: `kasir2` / `kasir123`
  - Supervisor: `supervisor1` / `kasir123`

## 📊 Database Schema

### Core Tables
- **customer**: Customer information and contact details
- **item**: Product catalog with pricing and inventory
- **sales**: Sales transaction headers
- **transaction**: Detailed transaction line items
- **transactio_temp**: Temporary transaction storage
- **petugas & manager**: User accounts with role-based access
- **level**: User access level definitions
- **identitas**: Company profile and branding information

### Key Relationships
- Sales → Customer (Many-to-One)
- Transaction → Sales (Many-to-One)
- Transaction → Item (Many-to-One)
- Users → Level (Many-to-One)

## 🏗️ System Architecture

### Directory Structure
```
pos-app/
├── api/                    # REST API endpoints
│   ├── dashboard_stats.php
│   ├── recent_transactions.php
│   └── low_stock.php
├── classes/                # Core business logic classes
│   ├── Auth.php           # Authentication & authorization
│   ├── BaseCRUD.php       # Base CRUD operations
│   ├── Customer.php       # Customer management
│   ├── Item.php           # Inventory management
│   └── Sales.php          # Sales & transaction management
├── config/                 # Configuration files
│   └── Database.php       # Database connection singleton
├── database/              # Database scripts
│   └── pos_database.sql   # Complete database structure
├── modules/               # Feature modules
│   ├── customer/          # Customer CRUD interface
│   ├── item/             # Item CRUD interface
│   ├── sales/            # Sales management interface
│   ├── transaction/      # Transaction processing
│   ├── petugas/          # Staff management (Admin only)
│   ├── reports/          # Analytics and reporting
│   └── profile/          # User profile management
├── dashboard.php          # Main dashboard
├── login.php             # Authentication interface
└── logout.php            # Session termination
```

### Design Patterns Used
- **Singleton Pattern**: Database connection management
- **Inheritance**: BaseCRUD class for common operations
- **MVC Architecture**: Separation of concerns
- **Repository Pattern**: Data access abstraction

## 🎨 User Interface

### Design Principles
- **Consistent Bootstrap 5 Styling**: Professional and modern appearance
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Intuitive Navigation**: Sidebar navigation with breadcrumbs
- **Color-coded Status**: Visual indicators for stock levels and transaction status
- **Interactive Elements**: Real-time calculations and form validation

### Key UI Components
- **Dashboard**: Statistics cards, recent transactions, low stock alerts
- **Data Tables**: Sortable, searchable, paginated data views
- **Forms**: Comprehensive input validation and user feedback
- **Modals & Alerts**: User-friendly notifications and confirmations

## 🔐 Security Features

### Authentication & Authorization
- **Password Hashing**: BCrypt encryption for user passwords
- **Session Management**: Secure session handling with timeout
- **Role-based Access**: Different permission levels for different user types
- **CSRF Protection**: Form token validation (recommended for production)

### Data Security
- **SQL Injection Prevention**: Prepared statements with PDO
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: HTML entity encoding for output
- **Database Constraints**: Foreign key relationships and data integrity

## 📈 Business Logic

### Transaction Flow
1. **Order Creation**: Select customer and add items to cart
2. **Temporary Storage**: Items stored in `transactio_temp` table
3. **Stock Validation**: Real-time stock availability checking
4. **Transaction Completion**: Move from temp to permanent tables with DO number generation
5. **Inventory Update**: Automatic stock deduction and restoration on cancellation
6. **Invoice Generation**: Professional print-ready invoices with company branding
7. **Status Management**: Track transactions through pending, completed, and cancelled states

### Inventory Management
- **Real-time Stock Tracking**: Automatic updates on sales with transaction history
- **Low Stock Alerts**: Configurable threshold notifications (≤10 items)
- **Stock History**: Complete transaction-based inventory tracking
- **Profit Calculation**: Automatic margin and profit calculations with buy/sell price tracking
- **Multi-UOM Support**: Different units of measurement for various product types

## 🚀 Usage Guide

### For Administrators
1. **User Management**: Create and manage user accounts
2. **System Configuration**: Update company information and settings
3. **Data Oversight**: Access to all modules and reports
4. **Backup Management**: Regular database backups (recommended)

### For Managers
1. **Sales Monitoring**: View sales reports and analytics
2. **Inventory Control**: Monitor stock levels and reorder points
3. **Customer Relations**: Manage customer information and history
4. **Performance Analysis**: Generate business intelligence reports

### For Cashiers (Kasir)
1. **Transaction Processing**: Create new sales transactions
2. **Customer Service**: Look up customer information and history
3. **Inventory Queries**: Check product availability and pricing
4. **Daily Operations**: Process routine sales and customer interactions

## 🔧 Customization

### Adding New Features
1. Create new class in `classes/` directory extending `BaseCRUD`
2. Add corresponding module in `modules/` directory
3. Update navigation in dashboard and relevant pages
4. Add necessary database tables/columns

### Modifying UI
1. Update Bootstrap classes for styling changes
2. Modify CSS in individual page `<style>` sections
3. Add custom JavaScript for enhanced functionality
4. Update color schemes in gradient definitions

## 📊 Reporting Features

### Available Reports
- **Sales Summary**: Total sales, revenue, average sale value with status breakdown
- **Top 10 Best Selling Items**: Most popular products by quantity and revenue
- **Customer Analytics**: Top customers by purchase volume and spending
- **Low Stock Alerts**: Items requiring reorder with current stock levels
- **Monthly Sales Trends**: 6-month sales performance analysis
- **Inventory Valuation**: Stock value and movement reports

### Report Customization
- **Date Range Filtering**: Flexible date range selection for all reports
- **Real-time Data**: Live updates reflecting current system state
- **Visual Analytics**: Charts and graphs for trend analysis
- **Print-ready Formats**: Professional report layouts for management review

## 🐛 Troubleshooting

### Common Issues & Solutions
1. **Database Connection Error**: Check XAMPP MySQL service and database credentials in `config/Database.php`
2. **Permission Denied**: Ensure proper file permissions on web directory (755 for directories, 644 for files)
3. **Session Issues**: Clear browser cache and cookies, check PHP session configuration
4. **Stock Discrepancies**: Run inventory reconciliation queries and verify transaction data integrity
5. **Missing Transaction Data**: Use the database repair scripts to restore missing transaction records
6. **Array Key Errors**: Verify SQL query column names match array access keys in PHP code
7. **CRUD Operation Failures**: Check for proper data validation and remove non-database fields before updates

### Performance Optimization
1. **Database Indexing**: Add indexes on frequently queried columns
2. **Query Optimization**: Use EXPLAIN to analyze slow queries
3. **Caching**: Implement Redis or Memcached for session storage
4. **Image Optimization**: Compress and optimize product images

## 🤝 Contributing

### Development Guidelines
1. Follow PSR-4 autoloading standards
2. Use meaningful variable and function names
3. Comment complex business logic
4. Test all CRUD operations thoroughly
5. Validate all user inputs

### Code Standards
- **PHP**: Follow PSR-12 coding standards
- **HTML**: Use semantic HTML5 elements
- **CSS**: Follow BEM methodology for class naming
- **JavaScript**: Use ES6+ features where supported

## 📝 License

This project is developed for educational and cooperative use. Please ensure compliance with local regulations regarding point-of-sale systems and data privacy.

## 📞 Support

For technical support or feature requests, please refer to the system documentation or contact the development team.

## 🎯 System Status

### ✅ Fully Functional Modules
- **Authentication System**: Role-based login with secure session management
- **Dashboard**: Real-time statistics and navigation
- **Customer Management**: Complete CRUD with search and pagination
- **Item Management**: Inventory tracking with stock alerts
- **Sales Management**: Full transaction processing with invoice generation
- **Transaction Processing**: Multi-item sales with temporary storage
- **Petugas Management**: Staff administration (Admin only)
- **Reports & Analytics**: Comprehensive business intelligence
- **Profile Management**: User account settings

### 🔧 Recent Bug Fixes
- Fixed missing sales edit functionality (404 error resolved)
- Resolved transaction data loading issues in view/print functions
- Fixed reports module array key errors
- Corrected petugas edit validation issues
- Enhanced database integrity with complete sample data

### 📊 Sample Data Included
- **25 Complete Sales Records** with full transaction details
- **35 Product Items** across multiple categories (Office, Cleaning, Kitchen, Electronics, Furniture)
- **22 Customer Records** with complete contact information
- **5 Staff Accounts** with different access levels
- **Company Profile** with branding information

---

**Version**: 1.2.0  
**Last Updated**: September 21, 2025  
**Status**: Production Ready  
**Developed for**: Employee Cooperatives - Household Goods and Equipment Procurement  
**Technology Stack**: PHP 8.0, MySQL, Bootstrap 5.1.3, XAMPP 8.0  
**Database Records**: 25 Sales, 35 Items, 22 Customers, 5 Staff Members

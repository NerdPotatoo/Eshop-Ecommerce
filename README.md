# 🛒 E-Shop - Complete E-Commerce Platform

> A fully functional e-commerce application built with PHP (Custom MVC), MySQL, and Tailwind CSS with comprehensive admin panel and customer features.

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Project Structure](#-project-structure)
- [Usage](#-usage)
- [Database Schema](#-database-schema)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)

---

## ✨ Features

### 🔐 Admin Panel
- **Dashboard Analytics**
  - Real-time sales metrics
  - Order statistics
  - Customer insights
  - Product inventory overview
  
- **Product Management**
  - Full CRUD operations
  - Image upload with validation
  - Category management
  - Stock tracking
  - Search and filters

- **Order Management**
  - View all orders with details
  - Update order status (Pending, Processing, Completed, Cancelled)
  - Customer information display
  - Order item breakdown

- **Customer Management**
  - View registered users
  - Order history per customer
  - Customer statistics

- **Contact Management**
  - View customer inquiries
  - Mark as read/replied/closed
  - Email notifications

### 🛍️ Customer Features
- **User Authentication**
  - Secure registration and login
  - Session management
  - Password hashing (bcrypt)
  
- **Product Browsing**
  - Featured products showcase
  - Category filters
  - Search functionality
  - Sort by price, name, newest
  - Pagination

- **Shopping Cart**
  - Add/Update/Remove items
  - Real-time cart count
  - Stock validation
  - Session-based storage

- **Checkout & Orders**
  - Shipping information form
  - Payment method selection
  - Order confirmation
  - Order history

- **Additional Pages**
  - About Us with team information
  - Contact form
  - Responsive design

---

## 🚀 Tech Stack

### Backend
- **PHP 8.1+** - Server-side scripting
- **MySQL/MariaDB** - Database
- **PDO** - Database abstraction layer
- **Composer** - Dependency management
- **PSR-4 Autoloading** - Class autoloading

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Font Awesome** - Icons
- **Vanilla JavaScript** - DOM manipulation & AJAX

### Architecture
- **Custom MVC Pattern**
- **Namespaced Controllers**
- **RESTful routing**
- **Session-based authentication**

---

## 📥 Installation

### Prerequisites
- PHP 8.1 or higher
- MySQL 8.0 or MariaDB 10.5+
- Composer
- Web server (Apache/Nginx) or PHP built-in server

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/eshop.git
cd eshop
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Database Setup
The database will be automatically created and seeded on first run. The configuration file will:
- Create the `eshop_db` database
- Create all necessary tables
- Insert default admin user
- Insert sample categories

**Database Configuration** (if needed):
Edit `app/configs/Database.php` to update connection details:
```php
private $host = "localhost";
private $db_name = "eshop_db";
private $username = "root";
private $password = "";
```

### Step 4: Start the Server
```bash
# Using PHP built-in server
php -S localhost:8000

# Or place in your web server's document root
```

### Step 5: Access the Application
- **Homepage**: `http://localhost:8000`
- **Admin Panel**: `http://localhost:8000/?page=admin/login`

### Default Credentials
- **Admin**: `username: admin` / `password: admin123`

---
## 📁 Project Structure

```
Eshop/
├── .github/                    # GitHub configuration & CI/CD
│   ├── workflows/
│   │   └── php.yml
│   ├── ISSUE_TEMPLATE/
│   └── copilot-instructions.md
│
├── app/
│   ├── configs/
│   │   ├── Database.php        # Database connection handler
│   │   └── eshop_db.sql       # Database schema & seed data
│   └── controllers/            # Business logic controllers
│       ├── AdminAuthController.php
│       ├── UserAuthController.php
│       ├── ProductController.php
│       ├── CategoryController.php
│       ├── OrderController.php
│       ├── CustomerController.php
│       ├── ContactController.php
│       ├── CartController.php
│       └── HomeController.php
│
├── pages/
│   ├── admin/                  # Admin panel pages
│   │   ├── include/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── login.php
│   │   ├── dashboard.php
│   │   ├── products.php
│   │   ├── add-product.php
│   │   ├── orders.php
│   │   ├── customers.php
│   │   ├── contacts.php
│   │   └── setup.php
│   │
│   ├── include/                # Public layout components
│   │   ├── header.php
│   │   └── footer.php
│   │
│   ├── index.php              # Homepage
│   ├── products.php           # Product listing
│   ├── about.php              # About page
│   ├── contact.php            # Contact form
│   ├── cart.php               # Shopping cart
│   ├── checkout.php           # Checkout process
│   ├── order-confirmation.php # Order success
│   ├── login.php              # User login
│   ├── signup.php             # User registration
│   └── logout.php             # Logout handler
│
├── assets/
│   └── images/                # Static images
│
├── uploads/
│   └── products/              # Product images
│
├── vendor/                    # Composer dependencies
│
├── action.php                 # Application router
├── index.php                  # Entry point
├── composer.json              # Dependencies
└── README.md                  # This file
```

---

## 💾 Database Schema

### Tables (8 total)

#### 1. `admins`
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR)
- password_hash (VARCHAR)
- created_at (TIMESTAMP)
```

#### 2. `users`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password_hash (VARCHAR)
- address (TEXT)
- phone (VARCHAR)
- created_at (TIMESTAMP)
```

#### 3. `categories`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR, UNIQUE)
- description (TEXT)
- created_at (TIMESTAMP)
```

#### 4. `products`
```sql
- id (INT, PRIMARY KEY)
- title (VARCHAR)
- description (TEXT)
- price (DECIMAL)
- image (VARCHAR)
- category_id (INT, FOREIGN KEY)
- stock (INT)
- created_at (TIMESTAMP)
```

#### 5. `orders`
```sql
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- total (DECIMAL)
- status (ENUM: Pending, Processing, Completed, Cancelled)
- shipping_info (TEXT, JSON)
- payment_method (VARCHAR)
- created_at (TIMESTAMP)
```

#### 6. `order_items`
```sql
- id (INT, PRIMARY KEY)
- order_id (INT, FOREIGN KEY)
- product_id (INT, FOREIGN KEY)
- quantity (INT)
- price (DECIMAL)
```

#### 7. `contacts`
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR)
- phone (VARCHAR)
- subject (VARCHAR)
- message (TEXT)
- subscribe (BOOLEAN)
- status (ENUM: New, Read, Replied, Closed)
- created_at (TIMESTAMP)
```

---

## 🔌 API Endpoints

### AJAX Endpoints
- `POST /action.php?page=add-to-cart` - Add product to cart
- `GET /action.php?page=cart-count` - Get cart item count

### Page Routes
```
?page=home                  - Homepage
?page=products              - Product listing
?page=about                 - About page
?page=contact               - Contact form
?page=cart                  - Shopping cart
?page=checkout              - Checkout
?page=order-confirmation    - Order success
?page=login                 - User login
?page=signup                - User registration
?page=logout                - Logout

Admin Routes:
?page=admin/login           - Admin login
?page=admin/dashboard       - Admin dashboard
?page=admin/products        - Product management
?page=admin/add-product     - Add/Edit product
?page=admin/orders          - Order management
?page=admin/customers       - Customer list
?page=admin/contacts        - Contact inquiries
```

## 📖 Usage

### Admin Panel

1. **Login**: Navigate to `http://localhost:8000/?page=admin/login`
2. **Dashboard**: View sales metrics, recent orders, and top products
3. **Products**: 
   - Add new products with images
   - Edit existing products
   - Delete products
   - Search and filter
4. **Orders**: 
   - View all customer orders
   - Update order status
   - View order details
5. **Customers**: View registered users and their order history
6. **Contacts**: View and manage customer inquiries

### Customer Flow

1. **Browse Products**: 
   - Visit homepage to see featured products
   - Navigate to Products page
   - Use filters (category, search, sort)

2. **Add to Cart**:
   - Click "Add to Cart" on any product
   - Cart count updates in header
   - Toast notification confirms addition

3. **Manage Cart**:
   - View cart at `?page=cart`
   - Update quantities
   - Remove items
   - Clear cart

4. **Checkout**:
   - Must be logged in
   - Fill shipping information
   - Select payment method (Cash on Delivery)
   - Place order

5. **Order Confirmation**:
   - View order details
   - Order number displayed
   - Order status tracking

---

## 🔒 Security Features

- **Password Hashing**: Bcrypt with cost factor 10
- **SQL Injection Prevention**: Prepared statements (PDO)
- **XSS Protection**: `htmlspecialchars()` on all outputs
- **Session Management**: Secure session handling
- **Authentication Middleware**: Route protection
- **CSRF Protection**: (Recommended to implement)
- **Input Validation**: Server-side validation
- **Stock Validation**: Prevents overselling

---

## 🎨 Design Features

- **Responsive Design**: Mobile-first approach
- **Modern UI**: Tailwind CSS utility classes
- **Toast Notifications**: User feedback for actions
- **Loading States**: Button states during async operations
- **Empty States**: Graceful handling of no data
- **Error Messages**: Clear user-friendly messages
- **Accessibility**: Semantic HTML

---

## 🧪 Testing

The project includes GitHub Actions workflow for:
- PHP syntax checking
- Code quality validation
- Composer dependency checks

### Run Locally
```bash
# Check PHP syntax
find . -name "*.php" -exec php -l {} \;

# Run composer validation
composer validate
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/AmazingFeature`)
3. **Commit changes** (`git commit -m 'Add some AmazingFeature'`)
4. **Push to branch** (`git push origin feature/AmazingFeature`)
5. **Open a Pull Request**

### Coding Standards
- Follow PSR-4 autoloading standards
- Use meaningful variable and function names
- Comment complex logic
- Maintain consistent indentation
- Write descriptive commit messages

---

## 🐛 Known Issues & Future Enhancements

### Potential Improvements
- [ ] Implement CSRF token protection
- [ ] Add email verification for registration
- [ ] Implement password reset functionality
- [ ] Add product reviews and ratings
- [ ] Implement wishlist feature
- [ ] Add multiple payment gateways
- [ ] Implement order tracking
- [ ] Add admin analytics dashboard
- [ ] Implement coupon/discount system
- [ ] Add product variants (size, color)

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

## 👥 Team

- **Yasir** - Founder & CEO
- **Tithi** - COO & Designer
- **Nasfim** - Managing Director
- **Rakibul** - Product Manager

---

## 📞 Contact & Support

For questions, issues, or feedback:
- **Email**: support@eshop.com
- **Website**: [E-Shop](http://localhost:8000)
- **GitHub Issues**: [Report a bug](https://github.com/yourusername/eshop/issues)

---

## 🙏 Acknowledgments

- Tailwind CSS for the amazing utility-first CSS framework
- Font Awesome for comprehensive icon library
- PHP community for excellent documentation
- All contributors and testers

---

**Made with ❤️ by the E-Shop Team**
- Password: `` (empty)

### 3. Start Development Server
```bash
php -S localhost:8000
```

### 4. Access the Application
- **Public Site**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/action.php?page=admin/login
- **Admin Credentials**: 
  - Username: `admin`
  - Password: `admin123`

## Key Features Implemented

### Security
- ✅ Password hashing using bcrypt
- ✅ SQL injection prevention with prepared statements
- ✅ XSS protection with htmlspecialchars
- ✅ Session-based authentication
- ✅ Authentication middleware

### Admin Features
- ✅ Full product CRUD operations
- ✅ Image upload handling
- ✅ Order status management
- ✅ Customer overview with statistics
- ✅ Real-time dashboard metrics

### Code Quality
- ✅ PSR-4 autoloading
- ✅ MVC architecture
- ✅ Namespace organization (App\Config, App\Controllers)
- ✅ Prepared statements for database queries
- ✅ Error handling

### CI/CD
- ✅ GitHub Actions workflow
- ✅ PHP syntax checking
- ✅ Composer validation
- ✅ Issue templates
- ✅ PR template

## Next Steps (Phase 2 Completion)

1. **User Authentication Pages**:
   - Update login.php and signup.php with UserAuthController integration
   - Add session checks to protected pages

2. **Product Display**:
   - Update pages/index.php to fetch and display products
   - Update pages/products.php with ProductController integration
   - Add pagination support

3. **Shopping Cart**:
   - Implement pages/cart.php with session-based cart
   - Add "Add to Cart" buttons on product pages
   - Create cart management functions

4. **Checkout**:
   - Create checkout page
   - Implement order placement logic
   - Add order confirmation and thank you page

## Controllers Available

### AdminAuthController
- `login($username, $password)` - Admin authentication
- `logout()` - Clear admin session
- `checkAuth()` - Middleware for protected routes

### UserAuthController
- `register($name, $email, $password, $address, $phone)` - User registration
- `login($email, $password)` - User authentication
- `logout()` - Clear user session
- `isLoggedIn()` - Check if user is logged in
- `getUserId()` - Get current user ID

### ProductController
- `getAllProducts()` - Fetch all products
- `getProductById($id)` - Get single product
- `createProduct($data)` - Add new product
- `updateProduct($id, $data)` - Update product
- `deleteProduct($id)` - Delete product
- `searchProducts($searchTerm)` - Search products

### OrderController
- `getAllOrders()` - Fetch all orders with customer info
- `getOrderById($id)` - Get order details
- `getOrderItems($orderId)` - Get order line items
- `updateOrderStatus($id, $status)` - Update order status

### CustomerController
- `getAllCustomers()` - Fetch all customers with statistics
- `getCustomerById($id)` - Get customer details
- `getCustomerOrders($customerId)` - Get customer's orders

## Contributing
Please refer to the GitHub templates in `.github/` for bug reports and feature requests.

## License
MIT

---

**Project Status**: Phase 1 (Admin Panel) - ✅ COMPLETE | Phase 2 (Public Website) - ⏳ IN PROGRESS

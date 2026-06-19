Github : https://github.com/RC-WEDE6021-PART-2-AND-PART-3/rc-pta-wede6021-part2-and-3-threadtitans

# Pastimes - Second-Hand Branded Clothing Marketplace

**Key Features:**
- User Registration and Authentication
- Product Browsing with Category Filters
- Shopping Cart Functionality
- Secure Checkout with Order Generation
- Order History with Total Calculation
- Seller Product Listing
- Admin Dashboard with User and Product Management
- Admin Communication System

### Step 1: Prerequisites

Ensure you have the following software installed:

- **XAMPP** (or WAMP/MAMP) - Download from [apachefriends.org](https://www.apachefriends.org/)
- **Web Browser** (Chrome, Firefox, Edge, etc.)
- **Text Editor** (VS Code, Sublime Text, Notepad++, etc.)

### Step 2: Start the Servers

1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache** (Web Server)
3. Click **Start** for **MySQL** (Database Server)

### Step 3: Set Up the Database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **New** on the left sidebar
3. Enter database name: **`pastimes`**
4. Select collation: `utf8_general_ci`
5. Click **Create**
6. Click the **Import** tab
7. Click **Choose File** and select the `pastimes_new.sql` file
8. Click **Go** at the bottom of the page

### Step 4: Run the Application

1. Copy the entire `pastimes` folder to:
   - **XAMPP:** `C:\xampp\htdocs\`
   - **WAMP:** `C:\wamp\www\`
   - **MAMP:** `/Applications/MAMP/htdocs/`

2. Open your browser and go to: `http://localhost/pastimes/`

---

## Required Software

| Software | Version | Purpose |
|----------|---------|---------|
| **XAMPP** | 7.4.x or 8.x | Web server and database server |
| **PHP** | 7.4+ | Server-side scripting |
| **MySQL** | 5.7+ | Database management |
| **Web Browser** | Latest version | Viewing the application |
| **Text Editor** | Any | Viewing/editing source code |

## Login Credentials for testing

1. Admin : karabo@pastimes.com   password123
2. Admin : admin@clothingstore   password123
3. User (Seller) : ratanang@pastimes.com  password123
4. User (Buyer) : cecilia@pastimes.com   password123

## How to Create a New Account

1. Go to http://localhost/pastimes/register.php
2. Fill in the registration form
3. Wait for admin verification (or use admin credentials to verify)

## How to test the Application

- Buyer
1. Register as a buyer
2. Wait for admin verification (or login as admin to verify)
3. Login with buyer credentials
4. Browse products
5. Add items to cart
6. View cart and update quantities
7. Checkout
8. View order confirmation with order number
9. View order history

- Seller
1. Register as a seller
2. Wait for admin verification
3. Login with seller credentials
4. Click "Add New Item"
5. Fill in product details and upload image
6. Submit for review
7. Wait for admin approval
8. Product appears on shop page

- Admin
1. Login as admin (karabo@pastimes.com / password123)
2. View dashboard statistics
3. Click "Users" to verify pending users
4. Click "Products" to approve pending products
5. Add, edit, or delete products
6. Send communications to users

## Important Notes 
Database Requirements

1. The database name must be pastimes (case-sensitive)
2. The SQL file pastimes_new.sql contains the complete database structure with sample data
3. All foreign key constraints are set with ON DELETE CASCADE for data integrity
4. The database includes sample products, users, and orders for testing
5. Product images are stored in uploads/products/
6. Category icons are stored in images/categories/
7. The hero banner image is stored in images/banners/hero-banner.jpg

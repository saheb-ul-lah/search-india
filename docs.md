# Search India (FindIt) - Local Business Directory Platform 🚀

[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)](https://github.com/saheb-ul-lah/search-india)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-8892BF.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL-00758F.svg)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Frontend-TailwindCSS%20%7C%20JS-38B2AC.svg)](https://tailwindcss.com/)
[![License](https://img.shields.io/github/license/saheb-ul-lah/search-india)](https://github.com/saheb-ul-lah/search-india/blob/main/LICENSE)
[![GitHub Repository](https://img.shields.io/badge/GitHub-View%20Repo-blue?logo=github)](https://github.com/saheb-ul-lah/search-india)
[![Contact](https://img.shields.io/badge/Say%20Hi!-iamsaheb786182%40gmail.com-brightgreen)](mailto:iamsaheb786182@gmail.com)

**Search India (FindIt)** is a comprehensive local business directory platform designed to connect users with businesses and services in their vicinity. It features a user-friendly public interface, a powerful admin panel for management, and a RESTful API for data extensibility.

---

## 🌟 Table of Contents

1.  [🎯 Key Features](#-key-features)
2.  [🌐 Live Demo](#-live-demo)
3.  [📸 Platform Showcase (Screenshots)](#-platform-showcase-screenshots)
4.  [💡 Technology Stack](#-technology-stack)
5.  [📁 Project Architecture & Structure](#-project-architecture--structure)
    *   [Core Components](#core-components)
    *   [Folder Structure](#folder-structure)
6.  [🚀 Core Modules & Functionalities](#-core-modules--functionalities)
    *   [Public-Facing Website](#public-facing-website)
    *   [Admin Panel (`justdial-admin`)](#admin-panel-justdial-admin)
    *   [RESTful API (`api/v1`)](#restful-api-apiv1)
7.  [🔧 Setup and Installation](#-setup-and-installation)
    *   [Prerequisites](#prerequisites)
    *   [Installation Steps](#installation-steps)
    *   [Quick Project Scaffolding (Optional)](#quick-project-scaffolding-optional)
    *   [Configuration](#configuration)
8.  [🤝 Contributing](#-contributing)
9.  [📜 License](#-license)
10. [📞 Contact](#-contact)

---

## 🎯 Key Features

*   **Advanced Business Search:** Keyword, location, and category-based search.
*   **Categorized Listings:** Businesses organized into relevant categories.
*   **Detailed Business Profiles:** Comprehensive information including images, services, contact details, and user reviews.
*   **User Reviews & Ratings:** Authentic feedback system.
*   **Featured Listings:** Option to highlight premium businesses.
*   **Secure User Authentication:** For both public users and administrators.
*   **Comprehensive Admin Dashboard:** For managing listings, users, categories, site settings, reviews, and inquiries.
*   **Responsive Design:** Optimized for various screen sizes.
*   **RESTful API:** For data access and potential third-party integrations.

---

## 🌐 Live Demo

*   **Public Website:** [https://searchindia.itsoftwaretech.com/](https://searchindia.itsoftwaretech.com/)
*   **Admin Panel:** [https://searchindia.itsoftwaretech.com/justdial-admin/](https://searchindia.itsoftwaretech.com/justdial-admin/)
    *   *(Admin credentials required for full access)*

---

## 📸 Platform Showcase (Screenshots)

Here's a glimpse into the Search India platform. *You will replace these placeholders with your actual screenshots.*

| Page / View                    | Screenshot                                                                    |
| :----------------------------- | :---------------------------------------------------------------------------- |
| **Public Website**             |                                                                               |
| Homepage                       | ![Public Homepage](path/to/your/image/screenshot-public-homepage.png)         |
| Business Listing/Search Results| ![Public Business Listing](path/to/your/image/screenshot-public-listing.png)    |
| Business Detail Page (Modal)   | ![Public Business Detail](path/to/your/image/screenshot-public-business-detail.png) |
| **Admin Panel**                |                                                                               |
| Login Page                     | ![Admin Login](path/to/your/image/screenshot-admin-login.png)                 |
| Dashboard Overview             | ![Admin Dashboard](path/to/your/image/screenshot-admin-dashboard.png)         |
| Business Management (List)     | ![Admin Business List](path/to/your/image/screenshot-admin-business-list.png) |
| Category Management (List)   | ![Admin Category List](path/to/your/image/screenshot-admin-category-list.png) |
| User Management (List)         | ![Admin User List](path/to/your/image/screenshot-admin-user-list.png)         |
| Site Settings                  | ![Admin Settings](path/to/your/image/screenshot-admin-settings.png)           |
| **API Interaction**            |                                                                               |
| API Request in Postman         | ![API Postman Example](path/to/your/image/screenshot-api-postman.png)         |

---

## 💡 Technology Stack

*   **Backend:** PHP (Procedural, PDO for DB)
*   **Frontend:** HTML5, Tailwind CSS, JavaScript (Vanilla JS, AOS, SwiperJS)
*   **Database:** MySQL
*   **Web Server:** Apache (with `.htaccess`) / Nginx compatible
*   **API:** Custom RESTful API (JSON)

---

## 📁 Project Architecture & Structure

### Core Components

The project is divided into three main parts:

1.  **Public-Facing Website (`index.php`, `justdial-public/`):** The primary interface for users to discover and interact with business listings.
2.  **Admin Panel (`justdial-admin/`):** A secure backend for administrators to manage all platform data and settings.
3.  **RESTful API (`api/`):** Provides structured data access for external applications or services.

### Folder Structure

```
justdial/
├── api/                        # RESTful API
│   ├── v1/
│   │   ├── config/
│   │   ├── includes/
│   │   ├── handlers/
│   │   ├── .htaccess
│   │   └── index.php
├── justdial-admin/             # Admin Panel
│   ├── assets/
│   ├── config/
│   ├── includes/
│   ├── modules/
│   ├── uploads/
│   ├── vendor/                 # Potentially for PHP dependencies if used
│   ├── index.php
│   ├── .htaccess
│   └── db.sql
├── justdial-public/            # Public facing components (if distinct)
│   ├── includes/
│   └── index.php
├── index.php                   # Main public website entry point
├── .htaccess                   # Root URL Rewriting
└── db.sql                      # Main database schema
```

---

## 🚀 Core Modules & Functionalities

### Public-Facing Website

*   **Business Discovery:** Users can search for businesses by keywords, categories, and locations.
*   **Listing & Detail Views:** View search results and access detailed pages/modals for individual businesses, including descriptions, images, contact information, operating hours, and user reviews.
*   **User Interaction:** Users can register, log in, and submit reviews for businesses.
*   **Dynamic Content:** Features like popular categories, featured businesses, and testimonials enhance user engagement.

### Admin Panel (`justdial-admin`)

A comprehensive backend system for platform management:

*   **Authentication:** Secure login for administrators.
*   **Dashboard:** Overview of key platform metrics and activities.
*   **Business Management:** CRUD operations for business listings (add, view, edit, delete).
*   **Category Management:** Manage business categories and subcategories.
*   **City Management:** Manage city and location data.
*   **Service Management:** Define and manage services offered by businesses.
*   **User Management:** Manage registered user accounts (customers and business owners).
*   **Inquiry Management:** View and manage user inquiries submitted through contact forms.
*   **Review Management:** Moderate and manage user-submitted reviews.
*   **Settings Management:** Configure site-wide settings (e.g., site name, logo, theme colors, API keys).
*   **File Uploads:** Manage uploaded media for businesses, categories, etc.

### RESTful API (`api/v1`)

The API allows programmatic access to the platform's data.

*   **Authentication:** All API endpoints require a valid `X-API-KEY` in the request header for authorization.
*   **Data Retrieval:** Endpoints available for fetching businesses, categories, cities, and site settings.
*   **Filtering & Pagination:** Supported on list endpoints for efficient data consumption.
*   **JSON Responses:** Data is returned in standard JSON format.
*   **Rate Limiting:** Implemented to prevent abuse and ensure fair usage.

➡️ **Full API Documentation:** [View API Docs on GitHub](https://github.com/saheb-ul-lah/search-india/blob/main/api/DOCS.md)

---

## 🔧 Setup and Installation

### Prerequisites
*   Web Server (Apache with `mod_rewrite` enabled, or Nginx)
*   PHP >= 7.4 (PDO, mbstring, json extensions recommended)
*   MySQL Database Server
*   Git (for cloning)

### Installation Steps

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/saheb-ul-lah/search-india.git
    cd search-india
    ```

2.  **Database Setup:**
    *   Create a MySQL database (e.g., `search_india_db`).
    *   Import the `db.sql` file (and `justdial-admin/db.sql` if it contains separate admin-specific tables/data) into your database.
        ```bash
        mysql -u your_mysql_user -p search_india_db < db.sql
        # If applicable:
        # mysql -u your_mysql_user -p search_india_db < justdial-admin/db.sql
        ```

3.  **Configure Database Connections:**
    *   **Admin Panel & Main App:** Edit `justdial-admin/config/database.php` with your DB credentials.
    *   **API:** Edit `api/v1/config/database.php` with your DB credentials.

4.  **Configure Base URLs:**
    *   Update `BASE_URL` in `justdial-admin/config/config.php` (e.g., `http://localhost/search-india/justdial-admin/`).
    *   Ensure URLs are correctly set for the public site (check root `index.php` or relevant configs).

5.  **Web Server Configuration:**
    *   **Apache:** Ensure `.htaccess` files are processed (`AllowOverride All`).
    *   **Nginx:** Convert `.htaccess` rules to Nginx server block configuration.

6.  **File Permissions:**
    *   Make the `justdial-admin/uploads/` directory and its subdirectories writable by the web server.
        ```bash
        chmod -R 775 justdial-admin/uploads/
        # (Optional) chown -R www-data:www-data justdial-admin/uploads/
        ```

### Quick Project Scaffolding (Optional)

If you need to recreate the directory structure for the admin panel quickly (e.g., for a fresh setup or understanding the layout), you can use these commands. **Note:** This only creates empty directories and files; it does not populate them with code.

```bash
# Creating Admin Panel Directories
mkdir -p justdial-admin/assets/css justdial-admin/assets/js justdial-admin/assets/fonts/montserrat justdial-admin/assets/fonts/poppins justdial-admin/assets/img/avatars
mkdir -p justdial-admin/config justdial-admin/includes justdial-admin/modules/auth justdial-admin/modules/dashboard
mkdir -p justdial-admin/modules/businesses justdial-admin/modules/categories justdial-admin/modules/cities justdial-admin/modules/services
mkdir -p justdial-admin/modules/users justdial-admin/modules/inquiries justdial-admin/modules/reviews justdial-admin/modules/settings
mkdir -p justdial-admin/uploads/businesses justdial-admin/uploads/categories justdial-admin/uploads/users
mkdir -p justdial-admin/vendor

# Creating Admin Panel Files
touch justdial-admin/assets/css/custom.css
touch justdial-admin/assets/js/chart-config.js justdial-admin/assets/js/sidebar.js justdial-admin/assets/js/main.js
touch justdial-admin/assets/img/logo.png justdial-admin/assets/img/favicon.ico

touch justdial-admin/config/config.php justdial-admin/config/database.php justdial-admin/config/functions.php
touch justdial-admin/includes/header.php justdial-admin/includes/footer.php justdial-admin/includes/sidebar.php justdial-admin/includes/auth.php

touch justdial-admin/modules/auth/login.php justdial-admin/modules/auth/register.php
touch justdial-admin/modules/auth/forgot-password.php justdial-admin/modules/auth/reset-password.php

touch justdial-admin/modules/dashboard/index.php

touch justdial-admin/modules/businesses/index.php justdial-admin/modules/businesses/add.php justdial-admin/modules/businesses/edit.php
touch justdial-admin/modules/businesses/view.php justdial-admin/modules/businesses/delete.php

touch justdial-admin/modules/categories/index.php justdial-admin/modules/categories/add.php
touch justdial-admin/modules/categories/edit.php justdial-admin/modules/categories/delete.php

touch justdial-admin/modules/cities/index.php # Add/edit/delete for cities if needed

touch justdial-admin/modules/services/index.php justdial-admin/modules/services/add.php
touch justdial-admin/modules/services/edit.php justdial-admin/modules/services/delete.php

touch justdial-admin/modules/users/index.php justdial-admin/modules/users/add.php justdial-admin/modules/users/edit.php
touch justdial-admin/modules/users/view.php justdial-admin/modules/users/delete.php

touch justdial-admin/modules/inquiries/index.php # Add/edit/view/delete for inquiries

touch justdial-admin/modules/reviews/index.php justdial-admin/modules/reviews/view.php justdial-admin/modules/reviews/delete.php

touch justdial-admin/modules/settings/index.php

touch justdial-admin/index.php
touch justdial-admin/.htaccess
touch justdial-admin/db.sql
```

### Configuration

*   **API Keys:** Managed as per `api/DOCS.md` and API configuration files.
*   **Site Settings:** Primarily managed via the Admin Panel -> Settings.
*   **Email Configuration:** Review `config.php` files or admin settings for mail server details if email functionality is used (e.g., password resets).

---

## 🤝 Contributing

Contributions are welcome! Please follow these general guidelines:

1.  Fork the repository (`https://github.com/saheb-ul-lah/search-india.git`).
2.  Create a new branch for your feature or bug fix.
3.  Develop and test your changes.
4.  Commit your changes with clear messages.
5.  Push to your forked repository.
6.  Open a Pull Request against the `main` branch of the original repository.

---

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/saheb-ul-lah/search-india/blob/main/LICENSE) file for details.

---

## 📞 Contact

Developed by **Saheb Ul Lah**.

*   **Email:** [iamsaheb786182@gmail.com](mailto:iamsaheb786182@gmail.com)
*   **Phone:** +91 8638232587
*   **GitHub:** [@saheb-ul-lah](https://github.com/saheb-ul-lah)

---

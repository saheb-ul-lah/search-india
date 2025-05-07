
# 🇮🇳 Search India (FindIt) - Your Ultimate Local Business Directory 🚀

![Search India Logo](https://searchindia.itsoftwaretech.com/justdial-admin/assets/img/logo.png) <!-- Replace with a nice banner logo -->

[![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)](https://github.com/saheb-ul-lah/search-india)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-8892BF.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL-00758F.svg)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Frontend-TailwindCSS%20%7C%20JS-38B2AC.svg)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE) <!-- Create a LICENSE file if you don't have one -->
[![Contact](https://img.shields.io/badge/Say%20Hi!-iamsaheb786182%40gmail.com-brightgreen)](mailto:iamsaheb786182@gmail.com)

Welcome to **Search India**, a comprehensive local business directory platform designed to connect users with the best businesses, services, restaurants, and professionals in their cities. Built with a robust PHP backend, a dynamic Tailwind CSS frontend, and a feature-rich admin panel, Search India aims to be the go-to solution for local discovery.

---

## 🌟 Table of Contents

1.  [✨ Project Overview](#-project-overview)
2.  [🎯 Key Features](#-key-features)
3.  [🌐 Live Demo](#-live-demo)
4.  [📸 Screenshots Showcase](#-screenshots-showcase)
    *   [🏠 Homepage Highlights](#-homepage-highlights)
    *   [🛠️ Admin Panel Glimpse](#️-admin-panel-glimpse)
    *   [🔌 API Interaction](#-api-interaction)
5.  [💡 Technology Stack](#-technology-stack)
6.  [📁 Folder Structure](#-folder-structure)
7.  [🚀 Core Functionalities & Pages](#-core-functionalities--pages)
    *   [🎨 Public-Facing Website (FindIt Homepage)](#-public-facing-website-findit-homepage)
        *   [Header & Navigation](#header--navigation)
        *   [Hero Section & Advanced Search](#hero-section--advanced-search)
        *   [Popular Categories](#popular-categories)
        *   [Interactive Tabs Section](#interactive-tabs-section)
        *   [Featured Businesses](#featured-businesses)
        *   [How It Works](#how-it-works)
        *   [Testimonials Slider](#testimonials-slider)
        *   [Popular Cities](#popular-cities)
        *   [Statistics Counter](#statistics-counter)
        *   [FAQ Accordion](#faq-accordion)
        *   [Newsletter Subscription](#newsletter-subscription)
        *   [App Download CTA](#app-download-cta)
        *   [Business Owner CTA](#business-owner-cta)
        *   [Comprehensive Footer](#comprehensive-footer)
        *   [Modals (Login, Signup, Business Details, Success)](#modals-login-signup-business-details-success)
        *   [User Experience Enhancements](#user-experience-enhancements)
    *   [⚙️ Admin Panel (justdial-admin)](#️-admin-panel-justdial-admin)
        *   [Authentication (Login, Register, Forgot/Reset Password)](#authentication-login-register-forgotreset-password)
        *   [Dashboard](#dashboard)
        *   [Business Management](#business-management)
        *   [Category Management](#category-management)
        *   [City Management](#city-management)
        *   [Service Management](#service-management)
        *   [User Management](#user-management)
        *   [Inquiry Management](#inquiry-management)
        *   [Review Management](#review-management)
        *   [Settings Management](#settings-management)
        *   [File Uploads](#file-uploads)
    *   [🔌 RESTful API (api/v1)](#-restful-api-apiv1)
        *   [API Authentication](#api-authentication)
        *   [API Endpoints Guide](#api-endpoints-guide)
        *   [Rate Limiting](#rate-limiting)
8.  [🔧 Setup and Installation](#-setup-and-installation)
    *   [Prerequisites](#prerequisites)
    *   [Installation Steps](#installation-steps)
    *   [Configuration](#configuration)
9.  [🤝 Contributing](#-contributing)
10. [📜 License](#-license)
11. [📞 Contact](#-contact)

---

## ✨ Project Overview

Search India (FindIt) is more than just a directory; it's a dynamic platform enabling users to:
*   **Discover** local businesses across various categories.
*   **Read and write** authentic reviews.
*   Get detailed information including contact details, addresses, and operational hours.
*   **Compare** services and make informed decisions.

For business owners, Search India provides a powerful tool to:
*   **List** their businesses and gain visibility.
*   **Manage** their online presence.
*   **Connect** with potential customers.
*   **Track** engagement through analytics.

The project is split into three main components:
1.  **Public-Facing Website (`justdial-public/` & root `index.php`):** The beautiful, user-friendly interface where users search and browse.
2.  **Admin Panel (`justdial-admin/`):** A comprehensive backend for administrators to manage all aspects of the platform.
3.  **RESTful API (`api/`):** Provides data access for potential mobile applications or third-party integrations.

---

## 🎯 Key Features

*   **Advanced Search:** Search by keyword, location, and category.
*   **Categorized Listings:** Businesses neatly organized into relevant categories and sub-categories.
*   **Detailed Business Profiles:** Comprehensive information, including images, services, maps, reviews, and contact details.
*   **User Reviews and Ratings:** Authentic feedback system to help users choose.
*   **Featured Listings:** Highlight premium or popular businesses.
*   **City-Specific Pages:** Explore businesses within specific cities.
*   **User Authentication:** Secure login and registration for users and business owners.
*   **Admin Dashboard:** Powerful control panel for managing listings, users, categories, settings, and more.
*   **Responsive Design:** Seamless experience across desktops, tablets, and mobile devices.
*   **RESTful API:** For extending functionality and data accessibility.
*   **Interactive UI/UX:** Modern design with animations, modals, and smooth navigation.
*   **SEO Friendly:** Structured for better search engine visibility.

---

## 🌐 Live Demo

Experience Search India (FindIt) live:

➡️ **Public Website:** [https://searchindia.itsoftwaretech.com/](https://searchindia.itsoftwaretech.com/)
➡️ **Admin Panel:** [https://searchindia.itsoftwaretech.com/justdial-admin/](https://searchindia.itsoftwaretech.com/justdial-admin/)
    *   *(Admin credentials required for full access)*

---

## 📸 Screenshots Showcase

A visual tour of the Search India (FindIt) platform.

### 🏠 Homepage Highlights

| Description                      | Screenshot                                                                   |
| :------------------------------- | :--------------------------------------------------------------------------- |
| Hero Section & Search Bar        | ![Homepage Hero Section](path/to/your/image/screenshot-homepage-hero.png)        |
| Popular Categories Grid          | ![Homepage Categories](path/to/your/image/screenshot-homepage-categories.png)    |
| Interactive Services Tabs        | ![Homepage Tabs Section](path/to/your/image/screenshot-homepage-tabs.png)        |
| Featured Business Listings       | ![Homepage Featured Businesses](path/to/your/image/screenshot-homepage-featured.png) |
| How It Works Steps               | ![Homepage How It Works](path/to/your/image/screenshot-homepage-howitworks.png)  |
| Testimonials Carousel            | ![Homepage Testimonials](path/to/your/image/screenshot-homepage-testimonials.png) |
| Popular Cities Grid              | ![Homepage Popular Cities](path/to/your/image/screenshot-homepage-cities.png)    |
| Statistics Section               | ![Homepage Statistics](path/to/your/image/screenshot-homepage-stats.png)         |
| FAQ Accordion                    | ![Homepage FAQ](path/to/your/image/screenshot-homepage-faq.png)                  |
| Newsletter Subscription          | ![Homepage Newsletter](path/to/your/image/screenshot-homepage-newsletter.png)    |
| App Download Section             | ![Homepage App Download](path/to/your/image/screenshot-homepage-appdl.png)       |
| Business Owner CTA               | ![Homepage Business CTA](path/to/your/image/screenshot-homepage-bizcta.png)      |
| Footer                           | ![Homepage Footer](path/to/your/image/screenshot-homepage-footer.png)            |
| Login Modal                      | ![Login Modal](path/to/your/image/screenshot-modal-login.png)                    |
| Signup Modal                     | ![Signup Modal](path/to/your/image/screenshot-modal-signup.png)                  |
| Business Details Modal           | ![Business Details Modal](path/to/your/image/screenshot-modal-business.png)      |

### 🛠️ Admin Panel Glimpse

| Description                            | Screenshot                                                                           |
| :------------------------------------- | :----------------------------------------------------------------------------------- |
| Admin Login Page                       | ![Admin Login](path/to/your/image/screenshot-admin-login.png)                        |
| Admin Dashboard Overview               | ![Admin Dashboard](path/to/your/image/screenshot-admin-dashboard.png)                |
| Businesses List View                   | ![Admin Businesses List](path/to/your/image/screenshot-admin-businesses-list.png)    |
| Add/Edit Business Form                 | ![Admin Add Business](path/to/your/image/screenshot-admin-businesses-edit.png)       |
| Categories List View                   | ![Admin Categories List](path/to/your/image/screenshot-admin-categories-list.png)    |
| Add/Edit Category Form                 | ![Admin Add Category](path/to/your/image/screenshot-admin-categories-edit.png)       |
| Cities List View                       | ![Admin Cities List](path/to/your/image/screenshot-admin-cities-list.png)            |
| Users List View                        | ![Admin Users List](path/to/your/image/screenshot-admin-users-list.png)              |
| Inquiries Management                   | ![Admin Inquiries](path/to/your/image/screenshot-admin-inquiries.png)                |
| Reviews Management                     | ![Admin Reviews](path/to/your/image/screenshot-admin-reviews.png)                    |
| General Settings Page                  | ![Admin Settings](path/to/your/image/screenshot-admin-settings.png)                  |

### 🔌 API Interaction

| Description                      | Screenshot                                                                   |
| :------------------------------- | :--------------------------------------------------------------------------- |
| Postman - API Key Header         | ![Postman API Key](path/to/your/image/screenshot-api-postman-key.png)            |
| Postman - GET Businesses Request | ![Postman Get Businesses](path/to/your/image/screenshot-api-postman-biz.png)     |
| Postman - GET Categories Request | ![Postman Get Categories](path/to/your/image/screenshot-api-postman-cat.png)   |

---

## 💡 Technology Stack

*   **Backend:** PHP (Procedural with PDO for database interaction)
*   **Frontend:** HTML5, Tailwind CSS, JavaScript (Vanilla JS, AOS for animations, SwiperJS for sliders)
*   **Database:** MySQL
*   **Web Server:** Apache (with `.htaccess` for URL rewriting) / Nginx (or any server supporting PHP)
*   **API:** Custom-built RESTful API with JSON responses
*   **Version Control:** Git & GitHub (implied)
*   **Admin Panel Styling:** Custom CSS, Font Awesome, Google Fonts (Montserrat, Poppins)

---

## 📁 Folder Structure

The project is organized into logical components for maintainability and scalability:

```
justdial/
├── api/                        # RESTful API
│   ├── v1/
│   │   ├── config/             # API specific config
│   │   │   ├── config.php
│   │   │   └── database.php
│   │   ├── includes/           # API helper functions
│   │   │   ├── functions.php
│   │   │   └── data_functions.php
│   │   ├── handlers/           # Resource-specific logic
│   │   │   ├── business_handler.php
│   │   │   ├── category_handler.php
│   │   │   ├── city_handler.php
│   │   │   └── setting_handler.php
│   │   ├── .htaccess
│   │   └── index.php           # API Router/Entry Point
├── justdial-admin/             # Admin Panel
│   ├── assets/                 # CSS, JS, Fonts, Images for Admin
│   │   ├── css/
│   │   ├── js/
│   │   ├── fonts/
│   │   └── img/
│   ├── config/                 # Admin config & core functions
│   │   ├── config.php
│   │   ├── database.php
│   │   └── functions.php
│   ├── includes/               # Reusable UI components (header, footer, sidebar)
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── sidebar.php
│   │   └── auth.php            # Authentication check for admin pages
│   ├── modules/                # Core admin functionalities
│   │   ├── auth/               # Login, Register, Password Reset
│   │   ├── dashboard/
│   │   ├── businesses/         # CRUD for Businesses
│   │   ├── categories/         # CRUD for Categories
│   │   ├── cities/             # CRUD for Cities
│   │   ├── services/           # CRUD for Services (associated with businesses)
│   │   ├── users/              # CRUD for Users
│   │   ├── inquiries/          # Manage user inquiries
│   │   ├── reviews/            # Manage business reviews
│   │   └── settings/           # Site-wide settings
│   ├── uploads/                # Directory for uploaded files
│   │   ├── businesses/
│   │   ├── categories/
│   │   └── users/
│   ├── index.php               # Admin Panel Entry Point / Router
│   ├── .htaccess               # Admin URL Rewriting
│   └── db.sql                  # Admin specific SQL (if any, or part of main)
├── justdial-public/            # Public facing website (if distinct from root index.php)
│   ├── includes/
│   │   ├── header.php
│   │   ├── footer.php
│   └── index.php               # Might be the actual homepage content if root index.php is a router
├── index.php                   # Main public website entry point / Homepage
├── .htaccess                   # Root URL Rewriting
└── db.sql                      # Main database schema
```

---

## 🚀 Core Functionalities & Pages

Let's dive deep into the heart of Search India (FindIt).

### 🎨 Public-Facing Website (FindIt Homepage)

The homepage is the gateway to discovering local businesses. It's designed to be engaging, intuitive, and packed with features.

#### Header & Navigation
*   **Logo & Branding:** Prominent "FindIt" logo with a unique initial icon.
    *   ![Homepage Header](path/to/your/image/screenshot-homepage-header.png)
*   **Desktop Navigation:** Clean and clear links to key sections: Home, Categories, Businesses, How It Works, Testimonials.
*   **Authentication Buttons:** Easy access to "Login" and "Sign Up" for users.
*   **Mobile-Responsive Menu:** A hamburger menu icon for smaller screens, revealing navigation links and auth buttons.
*   **Sticky Header:** The header remains visible on scroll for easy navigation.

#### Hero Section & Advanced Search
*   **Engaging Headline:** "Discover the Best Local Businesses Near You" to immediately convey the site's purpose.
    *   ![Homepage Hero Full](path/to/your/image/screenshot-homepage-hero-full.png)
*   **Compelling Subtext:** Explains the value proposition to users.
*   **Advanced Search Bar:**
    *   Input for "What are you looking for?" (e.g., service, business name).
    *   Input for "Location" (e.g., city, zip code).
    *   Prominent "Search" button with an arrow icon.
    *   Icons within input fields for better UX (search, map marker).
    *   Shadow and hover effects for a modern feel.
*   **Popular Searches:** Quick links to frequently searched categories like "Restaurants," "Hotels," etc.
*   **Visual Element (Desktop):** An illustrative image (placeholder `600x500`) with floating info cards ("Top Rated," "Verified Listings") to enhance visual appeal and trust.
*   **Animated Background Elements:** Subtle SVG patterns and a wave divider for a polished look.
*   **Scroll Down Indicator:** An animated chevron prompting users to explore further.

#### Popular Categories
*   **Clear Section Title:** "Popular Categories."
*   **Grid Layout:** Displays multiple categories in a visually appealing grid (2, 3, or 4 columns depending on screen size).
    *   ![Homepage Categories Grid Detail](path/to/your/image/screenshot-homepage-categories-detail.png)
*   **Category Cards:** Each card includes:
    *   An icon representing the category (e.g., `fa-utensils` for Restaurants).
    *   Category Name.
    *   Number of listings in that category.
    *   Hover effects (shadow, icon scale) for interactivity.
*   **"View All Categories" Link:** Takes users to a page listing all available categories.

#### Interactive Tabs Section
*   **Purpose:** To showcase different ways users can benefit from the platform.
*   **Tab Navigation:** Buttons for "Find Businesses," "Read Reviews," "List Your Business," and "Mobile App."
    *   ![Homepage Tabs Navigation](path/to/your/image/screenshot-homepage-tabs-nav.png)
*   **Tab Content:** Each tab reveals:
    *   A descriptive title and paragraph.
    *   Bulleted list of benefits/features.
    *   A relevant call-to-action button.
    *   An illustrative image (placeholder `600x400`).
    *   Smooth fade-in animation when switching tabs.
    *   ![Homepage Tab Content Example](path/to/your/image/screenshot-homepage-tabs-content.png)

#### Featured Businesses
*   **Section Goal:** Highlight top-rated or sponsored businesses.
*   **Card Layout:** Displays businesses in a grid (1, 2, or 3 columns).
    *   ![Homepage Featured Business Card](path/to/your/image/screenshot-homepage-featured-card.png)
*   **Business Cards:** Each card features:
    *   Business Image (placeholder `600x400`).
    *   "Featured" badge.
    *   Star rating.
    *   Category tag.
    *   Business Name (clickable, opening a business details modal or page).
    *   Location (with map marker icon).
    *   Phone number (with phone icon).
    *   Review count.
    *   Hover effects for enhanced interactivity.
*   **"Explore All Businesses" Button:** Links to a comprehensive business listing page.

#### How It Works
*   **Simple Explanation:** Clearly outlines the user journey in three steps: Search, Compare, Connect.
    *   ![Homepage How It Works Detail](path/to/your/image/screenshot-homepage-howitworks-detail.png)
*   **Iconography:** Each step is accompanied by a relevant icon within a styled circle.
*   **Call-to-Action Buttons:** "Sign Up Now" and "Learn More" to encourage user engagement.

#### Testimonials Slider
*   **Social Proof:** Showcases positive feedback from users.
    *   ![Homepage Testimonial Slide](path/to/your/image/screenshot-homepage-testimonial-slide.png)
*   **SwiperJS Carousel:**
    *   Displays one testimonial at a time.
    *   Each slide includes:
        *   Star rating for the platform.
        *   Testimonial text (italicized).
        *   User's avatar (placeholder `100x100`), name, and location.
    *   Autoplay functionality.
    *   Navigation arrows (previous/next).
    *   Pagination dots.

#### Popular Cities
*   **Localized Discovery:** Helps users find businesses in major urban areas.
*   **Grid Layout:** Presents cities in a compact grid.
    *   ![Homepage Popular City Tile](path/to/your/image/screenshot-homepage-city-tile.png)
*   **City Tiles:** Each tile displays:
    *   City Name.
    *   State Abbreviation.
    *   Number of businesses listed in that city.
    *   Hover effects (background change, scale).
*   **"View All Cities" Link:** Directs to a page listing all supported cities.

#### Statistics Counter
*   **Growth & Scale:** Visually represents the platform's reach.
    *   ![Homepage Statistics Counters](path/to/your/image/screenshot-homepage-stats-counters.png)
*   **Animated Counters:** Numbers animate from 0 to their actual value for:
    *   Businesses
    *   Users
    *   Reviews
    *   Cities
*   **Parallax Background:** A background image with a parallax effect for visual depth.

#### FAQ Accordion
*   **Quick Answers:** Addresses common user queries.
    *   ![Homepage FAQ Item Expanded](path/to/your/image/screenshot-homepage-faq-item.png)
*   **Accordion Interface:**
    *   Questions are clickable.
    *   Clicking a question expands to show the answer.
    *   Chevron icon indicates open/closed state and rotates on click.
    *   Only one FAQ item is open at a time for a cleaner interface.
*   **"View All FAQs" Link:** Leads to a dedicated FAQ page.

#### Newsletter Subscription
*   **Engagement & Updates:** Allows users to subscribe for news and offers.
    *   ![Homepage Newsletter Form](path/to/your/image/screenshot-homepage-newsletter-form.png)
*   **Form:**
    *   Email input field.
    *   "Subscribe Now" button.
*   **Incentive (Right Panel on Desktop):** Lists reasons to subscribe (exclusive deals, new business alerts, etc.).
*   **Privacy Note:** Link to Privacy Policy.

#### App Download CTA
*   **Mobile Accessibility:** Promotes the mobile app.
    *   ![Homepage App Download Buttons](path/to/your/image/screenshot-homepage-appdl-buttons.png)
*   **App Store Badges:** "Download on the App Store" and "Get it on Google Play" buttons.
*   **Social Proof:** App rating (e.g., 4.8/5 from 10,000+ reviews).
*   **Visual:** Image of the app on a mobile device (placeholder `600x400`) with floating feature callouts.

#### Business Owner CTA
*   **For Businesses:** Encourages business owners to list their services.
    *   ![Homepage Business CTA Section](path/to/your/image/screenshot-homepage-bizcta-section.png)
*   **Value Proposition:** Bullet points highlighting benefits for businesses (increase visibility, showcase services, collect reviews).
*   **Call-to-Action Buttons:** "List Your Business" and "View Plans."
*   **Visual:** An image representing a happy business owner or a thriving business (placeholder `600x400`).

#### Comprehensive Footer
*   **Brand Reinforcement:** Logo and brief company description.
    *   ![Homepage Footer Detail](path/to/your/image/screenshot-homepage-footer-detail.png)
*   **Social Media Links:** Icons for Facebook, Twitter, Instagram, LinkedIn.
*   **Navigation Links:**
    *   Quick Links (Home, Categories, etc.).
    *   For Business (List Your Business, Pricing, etc.).
*   **Contact Information:** Address, phone, email, and business hours with icons.
*   **Copyright & Legal Links:** Copyright notice, Terms of Service, Privacy Policy, Cookie Policy.

#### Modals (Login, Signup, Business Details, Success)
All modals are designed with a consistent, modern look and feel:
*   **Overlay:** Dark, semi-transparent background.
*   **Content Box:** Rounded corners, shadow.
*   **Close Button:** Prominent 'X' icon.
*   **Smooth Transitions:** Fade and scale effects on open/close.

*   **Login Modal:**
    *   Email and password fields.
    *   "Remember me" checkbox.
    *   "Forgot password?" link.
    *   Social login options (Google, Facebook).
    *   Link to switch to Signup modal.
    *   ![Login Modal Full](path/to/your/image/screenshot-modal-login-full.png)

*   **Signup Modal:**
    *   First name, last name, email, password, confirm password fields.
    *   Terms of Service & Privacy Policy agreement checkbox.
    *   Link to switch to Login modal.
    *   ![Signup Modal Full](path/to/your/image/screenshot-modal-signup-full.png)

*   **Business Details Modal:**
    *   Accessed by clicking a featured business.
    *   Large cover image and business logo.
    *   Business name, rating, review count.
    *   Tags (e.g., "Restaurant," "Open Now").
    *   **Tabs/Sections within modal (scrollable):**
        *   About section with business description.
        *   Photo gallery.
        *   Reviews list with individual review details.
        *   Contact Information (address, phone, website, email).
        *   Business Hours.
    *   Action buttons: Call Now, Get Directions, Save.
    *   ![Business Details Modal Overview](path/to/your/image/screenshot-modal-business-overview.png)
    *   ![Business Details Modal Reviews](path/to/your/image/screenshot-modal-business-reviews.png)

*   **Success Modal:**
    *   Generic modal used to confirm actions (e.g., form submission, newsletter subscription).
    *   Success icon (checkmark).
    *   Customizable success message.
    *   "Close" button.
    *   ![Success Modal Example](path/to/your/image/screenshot-modal-success.png)

#### User Experience Enhancements
*   **AOS Animations:** Subtle scroll animations on various sections for a dynamic feel.
*   **Custom Scrollbar:** Styled scrollbar for a more branded look.
*   **Back to Top Button:** Appears on scroll, allowing users to quickly return to the page top.
*   **Scroll Indicator:** A thin progress bar at the top of the page showing scroll depth.
*   **Ripple Effect on Buttons:** Material Design-inspired click effect.
*   **Tailwind CSS Customizations:** Custom color palettes (primary, secondary, accent), font families (Montserrat, Poppins), and animations defined in `tailwind.config`.

---

### ⚙️ Admin Panel (`justdial-admin`)

The admin panel is the command center for managing the entire Search India platform. It's built for efficiency and comprehensive control.

#### Authentication (Login, Register, Forgot/Reset Password)
*   **Login Page:** Secure login form for administrators. Fields for email and password.
    *   ![Admin Login Page Detail](path/to/your/image/screenshot-admin-login-detail.png)
*   **(Optional) Register Page:** If admin registration is allowed directly.
    *   ![Admin Register Page (if applicable)](path/to/your/image/screenshot-admin-register.png)
*   **Forgot/Reset Password:** Standard password recovery mechanism.
    *   ![Admin Forgot Password](path/to/your/image/screenshot-admin-forgotpass.png)
    *   ![Admin Reset Password](path/to/your/image/screenshot-admin-resetpass.png)
*   **Auth Protection:** All admin modules are protected and require login (`auth.php`).

#### Dashboard (`justdial-admin/modules/dashboard/`)
*   **Overview:** Provides at-a-glance statistics and summaries.
    *   ![Admin Dashboard Widgets](path/to/your/image/screenshot-admin-dashboard-widgets.png)
*   **Key Metrics:**
    *   Total Businesses
    *   Total Users
    *   Total Categories
    *   Pending Reviews/Listings
    *   Recent Activities
*   **Charts/Graphs:** (e.g., using `chart-config.js`) Visual representation of data like new sign-ups, listing growth.
    *   ![Admin Dashboard Charts](path/to/your/image/screenshot-admin-dashboard-charts.png)

#### Business Management (`justdial-admin/modules/businesses/`)
*   **List Businesses (`index.php`):**
    *   Paginated table view of all businesses.
    *   Columns: Name, Category, City, Status (Approved, Pending), Actions.
    *   Search and filter capabilities.
    *   ![Admin Business Listing Table](path/to/your/image/screenshot-admin-biz-list-table.png)
*   **Add Business (`add.php`):**
    *   Comprehensive form to add new business details:
        *   Name, description, contact info (phone, email, website).
        *   Address (street, city, state, zip, map coordinates).
        *   Category selection (dropdown/multi-select).
        *   Services offered.
        *   Operating hours.
        *   Image uploads (logo, gallery images).
        *   Social media links.
        *   Featured status.
    *   ![Admin Add Business Form Fields](path/to/your/image/screenshot-admin-biz-add-form.png)
*   **Edit Business (`edit.php`):** Similar form to `add.php`, pre-filled with existing business data.
*   **View Business (`view.php`):** Displays all details of a specific business in a read-only format.
    *   ![Admin View Business Details](path/to/your/image/screenshot-admin-biz-view.png)
*   **Delete Business (`delete.php`):** Functionality to remove a business listing (with confirmation).

#### Category Management (`justdial-admin/modules/categories/`)
*   **List Categories (`index.php`):**
    *   Table view of all categories.
    *   Columns: Name, Slug, Parent Category (if hierarchical), Icon, Featured, Actions.
    *   Support for parent-child category relationships.
    *   ![Admin Category Listing Table](path/to/your/image/screenshot-admin-cat-list-table.png)
*   **Add Category (`add.php`):**
    *   Form fields: Name, Slug (auto-generated or manual), Parent Category, Description, Icon (e.g., Font Awesome class), Image Upload, Featured status.
    *   ![Admin Add Category Form](path/to/your/image/screenshot-admin-cat-add-form.png)
*   **Edit Category (`edit.php`):** Similar form, pre-filled for editing.
*   **Delete Category (`delete.php`):** Functionality to remove categories.

#### City Management (`justdial-admin/modules/cities/`)
*   **List Cities (`index.php`):**
    *   Table view of all cities.
    *   Columns: City Name, State, Country (if applicable), Is Featured, Actions.
    *   Potential for adding/editing cities if the list is dynamic. Otherwise, might be a static list populated via DB.
    *   ![Admin City Listing Table](path/to/your/image/screenshot-admin-city-list-table.png)
*   **(If dynamic) Add/Edit City:** Forms to manage city entries.

#### Service Management (`justdial-admin/modules/services/`)
*   **List Services (`index.php`):**
    *   Table view of services that can be associated with businesses.
    *   Columns: Service Name, Description, Category (it might belong to), Actions.
    *   This module helps standardize services offered by businesses.
    *   ![Admin Service Listing Table](path/to/your/image/screenshot-admin-service-list-table.png)
*   **Add/Edit/Delete Service:** CRUD operations for services.

#### User Management (`justdial-admin/modules/users/`)
*   **List Users (`index.php`):**
    *   Paginated table of all registered users (customers, business owners).
    *   Columns: Name, Email, Role, Registration Date, Status (Active, Banned), Actions.
    *   ![Admin User Listing Table](path/to/your/image/screenshot-admin-user-list-table.png)
*   **Add User (`add.php`):** Admin ability to create user accounts.
*   **Edit User (`edit.php`):** Modify user details, change role, status.
*   **View User (`view.php`):** Detailed view of a user's profile and activity.
*   **Delete User (`delete.php`):** Remove user accounts.

#### Inquiry Management (`justdial-admin/modules/inquiries/`)
*   **List Inquiries (`index.php`):**
    *   Table view of contact form submissions or direct inquiries from users.
    *   Columns: Sender Name, Email, Subject, Date, Status (Read, Unread, Replied), Business (if inquiry is for a specific business), Actions.
    *   ![Admin Inquiry Listing Table](path/to/your/image/screenshot-admin-inquiry-list-table.png)
*   **View Inquiry (`view.php`):** Read the full inquiry message.
*   **Reply/Manage Status:** Functionality to mark as read, reply (could link to email client or have an internal reply system).
*   **Delete Inquiry (`delete.php`):** Remove inquiries.

#### Review Management (`justdial-admin/modules/reviews/`)
*   **List Reviews (`index.php`):**
    *   Table of all user-submitted reviews for businesses.
    *   Columns: Business Name, User Name, Rating, Review Text (snippet), Date, Status (Approved, Pending, Rejected), Actions.
    *   ![Admin Review Listing Table](path/to/your/image/screenshot-admin-review-list-table.png)
*   **View Review (`view.php`):** Full review text and details.
*   **Approve/Reject/Delete Review:** Moderation tools for reviews.

#### Settings Management (`justdial-admin/modules/settings/`)
*   **General Settings (`index.php`):**
    *   Form to manage site-wide configurations:
        *   Site Name, Site Tagline.
        *   Logo & Favicon Upload.
        *   Theme Colors (Primary, Secondary, Accent).
        *   Contact Email, Address, Phone.
        *   Social Media Links.
        *   Pagination Limits.
        *   API Key Management (if settings allow regeneration or viewing).
        *   Email settings (SMTP server, etc.).
    *   ![Admin General Settings Form](path/to/your/image/screenshot-admin-settings-general.png)
    *   ![Admin Theme Settings Form](path/to/your/image/screenshot-admin-settings-theme.png)

#### File Uploads (`justdial-admin/uploads/`)
*   Dedicated directories for storing uploaded images and files related to:
    *   Businesses (logos, gallery photos)
    *   Categories (icons, banner images)
    *   Users (avatars)
    *   Site (logo, favicon from settings)
*   Proper file handling and security measures are crucial for this part.

---

### 🔌 RESTful API (`api/v1`)

The API provides a programmatic way to access Search India's data. It's versioned (v1) for future compatibility.

#### API Authentication
*   **Mandatory API Key:** All endpoints under `/api/v1/` **REQUIRE** a valid `X-API-KEY` header.
*   **Obtaining a Key:** Contact the developer (details provided in the initial API information).
*   **Security:**
    *   Keys are likely hashed in the database for security.
    *   The raw key is used by the client in the header.
*   **Error Responses for Auth:**
    *   `401 Unauthorized: API Key missing.`
    *   `401 Unauthorized: Invalid API Key.`

#### API Endpoints Guide
The API is designed to be RESTful, using standard HTTP methods.

**1. Root Endpoint (`/api/v1/`)**
   *   **Method:** `GET`
   *   **Description:** Welcome message, confirms API is reachable.
   *   **Screenshot:** ![API Root Response](path/to/your/image/screenshot-api-root.png)

**2. Settings Endpoint (`/api/v1/settings`)**
   *   **Method:** `GET`
   *   **Description:** Retrieves public site settings (name, colors, logo URL).
   *   **Screenshot:** ![API Settings Response](path/to/your/image/screenshot-api-settings.png)

**3. Businesses Endpoints (`/api/v1/businesses`)**
   *   `GET /businesses`: List all businesses (paginated).
     *   Supports `page` and `limit` query parameters.
     *   Supports `category` (slug or ID) filter.
     *   **Screenshot (List):** ![API List Businesses](path/to/your/image/screenshot-api-list-businesses.png)
   *   `GET /businesses/{id}`: Get a specific business by its ID.
     *   **Screenshot (Single):** ![API Get Single Business](path/to/your/image/screenshot-api-single-business.png)

**4. Categories Endpoints (`/api/v1/categories`)**
   *   `GET /categories`: List all categories (paginated).
     *   Supports `parent_id=null` (for top-level), `featured=1`.
     *   **Screenshot (List):** ![API List Categories](path/to/your/image/screenshot-api-list-categories.png)
   *   `GET /categories/{id_or_slug}`: Get a specific category by ID or slug.
     *   **Screenshot (Single):** ![API Get Single Category](path/to/your/image/screenshot-api-single-category.png)
   *   `GET /categories/{id_or_slug}/businesses`: List businesses within a specific category (paginated).
     *   **Screenshot (Businesses in Category):** ![API Businesses by Category](path/to/your/image/screenshot-api-biz-by-cat.png)

**5. Cities Endpoints (`/api/v1/cities`)**
   *   `GET /cities`: List all cities (paginated).
     *   Supports `featured=1`, `state={stateName}`.
     *   **Screenshot (List):** ![API List Cities](path/to/your/image/screenshot-api-list-cities.png)
   *   `GET /cities/{cityName}/{stateName}/businesses`: List businesses in a specific city and state (paginated).
     *   *(Note: City/State names in URL might need URL encoding)*
     *   **Screenshot (Businesses in City):** ![API Businesses by City](path/to/your/image/screenshot-api-biz-by-city.png)

**How to Test with Postman:**
1.  Open Postman.
2.  Select the HTTP method (e.g., `GET`).
3.  Enter the full endpoint URL (e.g., `https://searchindia.itsoftwaretech.com/api/v1/businesses`).
4.  Go to the "Headers" tab.
5.  Add `X-API-KEY` in the "KEY" field and your `YOUR_RAW_API_KEY` in the "VALUE" field.
    *   ![Postman Headers Setup](path/to/your/image/screenshot-api-postman-headers.png)
6.  Click "Send".

#### Rate Limiting
*   **Limit:** Currently set to 1000 requests per day per API key.
*   **Error Response:** `429 Too Many Requests: Daily rate limit exceeded.`
*   This is implemented in `api/v1/includes/functions.php` (the `checkRateLimit` function).

---

## 🔧 Setup and Installation

Follow these steps to set up the Search India project locally or on a server.

### Prerequisites
*   Web Server (Apache with `mod_rewrite` enabled, or Nginx)
*   PHP >= 7.4 (with PDO, mbstring, json extensions)
*   MySQL Database Server
*   Composer (if any PHP dependencies are managed via Composer, though not explicitly stated in the structure)
*   Git (for cloning the repository)

### Installation Steps

1.  **Clone the Repository:**
    ```bash
    git clone [searchindia](https://github.com/saheb-ul-lah/search-india)
    cd searchindia
    ```

2.  **Database Setup:**
    *   Create a new MySQL database (e.g., `search_india_db`).
    *   Import the `db.sql` file into your newly created database.
        ```bash
        mysql -u your_mysql_user -p search_india_db < db.sql
        ```
    *   If there's a separate `justdial-admin/db.sql`, determine if it needs to be imported as well or if it's part of the main `db.sql`.

3.  **Configure Database Connections:**
    *   **Main Application & Admin Panel:**
        *   Edit `justdial-admin/config/database.php`:
            Update `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` with your database credentials.
        *   Edit `justdial-admin/config/config.php`:
            Update `BASE_URL` to your local/server admin URL (e.g., `http://localhost/searchindia/justdial-admin/`).
            The public site URL might also be here or derived.
    *   **API:**
        *   Edit `api/v1/config/database.php`:
            Update `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
        *   Edit `api/v1/config/config.php`:
            Update `API_BASE_URL` if needed. Review API key settings and rate limit configurations.

4.  **Set Base URLs:**
    *   Ensure `BASE_URL` in `justdial-admin/config/config.php` is correct.
    *   The main `index.php` (homepage) and `justdial-public/` might need a base URL configuration as well, often defined as a constant or detected automatically. Check the `config.php` files.
    *   For the API, `API_BASE_URL` in `api/v1/config/config.php` should be correct.

5.  **Configure Web Server:**
    *   **Apache:** Ensure `.htaccess` files in the root, `justdial-admin/`, and `api/v1/` are processed (`AllowOverride All` in Apache config for the project directory). `mod_rewrite` must be enabled.
    *   **Nginx:** You'll need to convert the `.htaccess` rules to Nginx server block configurations.
        *Example Nginx rule (conceptual, needs adaptation):*
        ```nginx
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }
        location /api/v1/ {
            try_files $uri $uri/ /api/v1/index.php?$query_string;
        }
        location /justdial-admin/ {
            try_files $uri $uri/ /justdial-admin/index.php?$query_string;
        }
        ```

6.  **Set File Permissions:**
    *   Ensure the `justdial-admin/uploads/` directory and its subdirectories (`businesses/`, `categories/`, `users/`) are writable by the web server user.
        ```bash
        chmod -R 775 justdial-admin/uploads/
        chown -R www-data:www-data justdial-admin/uploads/  # Replace www-data with your web server user/group
        ```

7.  **(Optional) Composer Dependencies:**
    *   If the project were to use Composer for dependencies (e.g., for libraries not included), you would run:
        ```bash
        composer install
        ```

### Configuration

*   **API Keys:** Managed within the API's database/configuration. New API keys need to be generated and stored (hashed) appropriately. The `api/v1/config/config.php` may hold static keys or settings related to key generation/validation.
*   **Site Settings:** Most site-wide settings (name, logo, colors) are managed through the admin panel's "Settings" module.
*   **Email Configuration:** If the site sends emails (e.g., password resets, notifications), email settings (SMTP server, credentials) would need to be configured, likely in one of the `config.php` files or via the admin panel.

---

## 🤝 Contributing

Contributions are welcome! If you'd like to contribute to Search India (FindIt), please follow these steps:

1.  **Fork the repository.**
2.  **Create a new branch** for your feature or bug fix: `git checkout -b feature/your-feature-name` or `git checkout -b bugfix/issue-description`.
3.  **Make your changes.** Write clean, well-documented code.
4.  **Test your changes thoroughly.**
5.  **Commit your changes:** `git commit -m "feat: Implement amazing new feature"` or `git commit -m "fix: Resolve critical bug"`.
6.  **Push to your forked repository:** `git push origin feature/your-feature-name`.
7.  **Open a Pull Request** to the main repository's `develop` or `main` branch. Provide a clear description of your changes.

Please adhere to the existing coding style and conventions.

---

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
*(You'll need to create a `LICENSE` file with the MIT License text if you choose this license).*

---

## 📞 Contact

Developed by **Saheb Giri**.

*   **Email:** [iamsaheb786182@gmail.com](mailto:iamsaheb786182@gmail.com)
*   **Phone:** +91 8638232587
*   **(Optional) GitHub:** [your-github-profile-link](https://github.com/your-username)
*   **(Optional) LinkedIn:** [your-linkedin-profile-link](https://linkedin.com/in/your-profile)

Feel free to reach out with any questions, feedback, or collaboration inquiries!

---

This README is a living document. As Search India (FindIt) evolves, this guide will be updated to reflect new features and improvements. Thank you for exploring the project! 🎉


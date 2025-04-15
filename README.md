```
justdial
├── api/
│   ├── v1/
│   │   ├── config/
│   │   │   ├── config.php       # API specific config (DB creds, keys, limits)
│   │   │   └── database.php     # PDO connection for API
│   │   ├── includes/
│   │   │   ├── functions.php    # Core API helpers (response, auth, rate limit)
│   │   │   └── data_functions.php # Data fetching logic adapted for API
│   │   ├── handlers/          # Logic for specific resources
│   │   │   ├── business_handler.php
│   │   │   ├── category_handler.php
│   │   │   ├── city_handler.php
│   │   │   └── setting_handler.php
│   │   ├── .htaccess          # URL Rewriting rules
│   │   └── index.php          # Main API Router/Entry Point
├── justdial-admin/
│    ├── assets/
│    │   ├── css/
│    │   │   └── custom.css
│    │   ├── js/
│    │   │   ├── chart-config.js
│    │   │   ├── sidebar.js
│    │   │   └── main.js
│    │   ├── fonts/
│    │   │   ├── montserrat/
│    │   │   └── poppins/
│    │   └── img/
│    │       ├── logo.png
│    │       ├── favicon.ico
│    │       └── avatars/
│    ├── config/
│    │   ├── config.php
│    │   ├── database.php
│    │   └── functions.php
│    ├── includes/
│    │   ├── header.php
│    │   ├── footer.php
│    │   ├── sidebar.php
│    │   └── auth.php
│    ├── modules/
│    │   ├── auth/
│    │   │   ├── login.php
│    │   │   ├── register.php
│    │   │   ├── forgot-password.php
│    │   │   └── reset-password.php
│    │   ├── dashboard/
│    │   │   └── index.php
│    │   ├── businesses/
│    │   │   ├── index.php
│    │   │   ├── add.php
│    │   │   ├── edit.php
│    │   │   ├── view.php
│    │   │   └── delete.php
│    │   ├── categories/
│    │   │   ├── index.php
│    │   │   ├── add.php
│    │   │   ├── edit.php
│    │   │   └── delete.php
│    │   ├── cities/
│    │   │   ├── index.php
│    │   ├── services/
│    │   │   ├── index.php
│    │   │   ├── add.php
│    │   │   ├── edit.php
│    │   │   └── delete.php
│    │   ├── users/
│    │   │   ├── index.php
│    │   │   ├── add.php
│    │   │   ├── edit.php
│    │   │   ├── view.php
│    │   │   └── delete.php
│    │   ├── inquiries/
│    │   │   ├── index.php
│    │   │   ├── add.php
│    │   │   ├── edit.php
│    │   │   ├── view.php
│    │   │   └── delete.php
│    │   ├── reviews/
│    │   │   ├── index.php
│    │   │   ├── view.php
│    │   │   └── delete.php
│    │   └── settings/
│    │       └── index.php
│    ├── uploads/
│    │   ├── businesses/
│    │   ├── categories/
│    │   └── users/
│    ├── index.php
│    ├── .htaccess
│    └── db.sql
│
│
├── justdial-public/
│    ├── includes/
│    │   ├── header.php
│    │   ├── footer.php
│    └── index.php
├── index.php
├── .htaccess
└── db.sql
```

```
mkdir -p justdial-admin/assets/css justdial-admin/assets/js justdial-admin/assets/fonts/montserrat justdial-admin/assets/fonts/poppins justdial-admin/assets/img/avatars
mkdir -p justdial-admin/config justdial-admin/includes justdial-admin/modules/auth justdial-admin/modules/dashboard
mkdir -p justdial-admin/modules/businesses justdial-admin/modules/categories justdial-admin/modules/services
mkdir -p justdial-admin/modules/users justdial-admin/modules/reviews justdial-admin/modules/settings
mkdir -p justdial-admin/uploads/businesses justdial-admin/uploads/categories justdial-admin/uploads/users
mkdir -p justdial-admin/vendor

# Creating files
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

touch justdial-admin/modules/services/index.php justdial-admin/modules/services/add.php
touch justdial-admin/modules/services/edit.php justdial-admin/modules/services/delete.php

touch justdial-admin/modules/users/index.php justdial-admin/modules/users/add.php justdial-admin/modules/users/edit.php
touch justdial-admin/modules/users/view.php justdial-admin/modules/users/delete.php

touch justdial-admin/modules/reviews/index.php justdial-admin/modules/reviews/view.php justdial-admin/modules/reviews/delete.php

touch justdial-admin/modules/settings/index.php

touch justdial-admin/index.php
touch justdial-admin/.htaccess
touch justdial-admin/db.sql
```
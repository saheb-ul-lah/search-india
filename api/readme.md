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


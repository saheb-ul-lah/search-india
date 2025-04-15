** Folder Structure **

```├── api/
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

```

**ALL endpoints under `/api/v1/` currently REQUIRE a valid `X-API-KEY` header**. 
**📝NOTE : There are no public endpoints in the current structure.**

To obtain an API key, please contact the developer using the details below:

- **Phone:** +91 8638232587  
- **Email:** [iamsaheb786182@gmail.com](mailto:iamsaheb786182@gmail.com)

**How to Attach the API Key in Postman:**

1.  **Open Postman** and create a new request tab (+).
2.  **Select Method & Enter URL:** Choose the HTTP method (e.g., `GET`) and enter the full URL for the endpoint you want to test (e.g., `https://searchindia.itsoftwaretech.com/api/v1/businesses`).
3.  **Go to the "Headers" Tab:** This tab is usually located below the URL input bar.
4.  **Add the Header:**
    *   In the first empty row under "KEY", type `X-API-KEY` (case-insensitive, but convention is uppercase).
    *   In the corresponding row under "VALUE", paste the **RAW API Key** that you generated and gave to the "user" (the long random string, *not* the hashed version from the database).
    *   Ensure the checkbox to the left of the header row is **checked** (enabled).

    ![Postman Headers Tab Example](https://learning.postman.com/docs/sending-requests/requests/assets/headers.png)
    *(Imagine `X-API-KEY` instead of `Content-Type`)*

5.  **Send:** Click the "Send" button.

**Testing Each Endpoint (Using Postman):**

Remember to **add the `X-API-KEY` header to *every* request** as described above.

---

**1. Root Endpoint**

*   **Method:** `GET`
*   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/`
*   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
*   **Expected Status:** `200 OK`
*   **Expected Body (JSON):**
    ```json
    {
        "status": "success",
        "data": {
            "message": "Welcome to the Search India API v1"
        }
    }
    ```

---

**2. Settings Endpoint**

*   **Method:** `GET`
*   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/settings`
*   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
*   **Expected Status:** `200 OK`
*   **Expected Body (JSON):** (Values depend on your database)
    ```json
    {
        "status": "success",
        "data": {
            "site_name": "JustDial Admin",
            "site_tagline": "Business Listing & Directory Platform",
            "primary_color": "#3b82f6",
            "secondary_color": "#10b981",
            "accent_color": "#f59e0b",
            "logo_url": "https://searchindia.itsoftwaretech.com/justdial-admin/uploads/logos/67ec276700d1a.jpg"
        }
    }
    ```

---

**3. Businesses Endpoints**

*   **List Businesses (Default Page 1, Limit 10):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/businesses`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list of businesses.
        ```json
        {
            "status": "success",
            "data": [ { /* business object 1 */ }, { /* business object 2 */ }, /* ... */ ],
            "meta": { "pagination": { /* ... pagination details ... */ } }
        }
        ```

*   **List Businesses (Specific Page & Limit):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/businesses?page=2&limit=5`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Businesses 6-10 (if available), with updated pagination meta.

*   **Filter Businesses (by Category Slug):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/businesses?category=automotive`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of businesses in the 'automotive' category.

*   **Filter Businesses (by Category ID):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/businesses?category=10`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of businesses in category ID 10.

*   **Get Specific Business by ID:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/businesses/1` (Replace `1` with a valid business ID)
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Detailed object for business ID 1.
    *   **Test Not Found:** Try an invalid ID (e.g., 99999): `https://searchindia.itsoftwaretech.com/api/v1/businesses/99999`
    *   **Expected Status:** `404 Not Found`
    *   **Expected Body (JSON):** `{"status": "error", "message": "Business not found."}`

---

**4. Categories Endpoints**

*   **List Categories (Default):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list of categories.

*   **List Top-Level Categories:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories?parent_id=null`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of categories where `parent_id` is NULL.

*   **List Featured Categories:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories?featured=1`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of categories where `featured` is true.

*   **Get Specific Category by Slug:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories/restaurants`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Details for the 'restaurants' category.

*   **Get Specific Category by ID:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories/1`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Details for category ID 1.
    *   **Test Not Found:** Try `https://searchindia.itsoftwaretech.com/api/v1/categories/999`
    *   **Expected Status:** `404 Not Found`

*   **List Businesses by Category Slug:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories/education/businesses`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list of businesses in the 'education' category.

*   **List Businesses by Category ID:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/categories/5/businesses?limit=3`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list (3 per page) of businesses in category ID 5.

---

**5. Cities Endpoints**

*   **List Cities (Default):**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/cities`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list of cities.

*   **List Featured Cities:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/cities?featured=1`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of cities where `is_featured` is true.

*   **List Cities by State:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/cities?state=Assam`
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** List of cities in Assam.

*   **List Businesses by City/State:**
    *   **Method:** `GET`
    *   **URL:** `https://searchindia.itsoftwaretech.com/api/v1/cities/Dibrugarh/Assam/businesses` (Note: City/State names in URL might need URL encoding if they contain special characters, e.g., `%20` for space)
    *   **Headers:** `X-API-KEY`: `YOUR_RAW_API_KEY`
    *   **Expected Status:** `200 OK`
    *   **Expected Body (JSON):** Paginated list of businesses in Dibrugarh, Assam.

---

**6. Testing Authentication & Rate Limiting**

*   **Missing API Key:**
    *   Send any request (e.g., `GET /businesses`) *without* the `X-API-KEY` header.
    *   **Expected Status:** `401 Unauthorized`
    *   **Expected Body:** `{"status": "error", "message": "Unauthorized: API Key missing."}`

*   **Invalid API Key:**
    *   Send any request with an incorrect value in the `X-API-KEY` header (e.g., `X-API-KEY: incorrect_key`).
    *   **Expected Status:** `401 Unauthorized`
    *   **Expected Body:** `{"status": "error", "message": "Unauthorized: Invalid API Key."}`

*   **Rate Limit (Hard to test manually):**
    *   You would need to make >1000 requests rapidly with the same valid key. If you hit the limit:
    *   **Expected Status:** `429 Too Many Requests`
    *   **Expected Body:** `{"status": "error", "message": "Too Many Requests: Daily rate limit exceeded."}`

---

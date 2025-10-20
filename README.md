
# Products API (Yii2 Basic)

A RESTful API for `products` built on **Yii2 Basic**. Uses your existing MySQL database table:

```sql
products(id INT PK AI, name VARCHAR(255), inventory_count INT, cost DECIMAL(10,2), selling_price DECIMAL(10,2), supplier_id INT NULL)
```

## Quick Start

### 1) Prereqs
- PHP 7.4–8.2
- Composer
- MySQL (same DB you used before, e.g. `products_api`)
- Apache (XAMPP) or PHP built-in server

### 2) Install dependencies
From the project root:
```bash
composer install
```

> If vendor/ is missing after install, ensure Composer can download packages. This project includes `composer.json` (no vendor) to keep zip small.

### 3) Configure DB
Edit `config/db.php`:
```php
'dsn' => 'mysql:host=127.0.0.1;dbname=products_api',
'username' => 'root',
'password' => '',
```

### 4A) Run with PHP built-in
```bash
php -S localhost:8080 -t web
```
Visit: `http://localhost:8080/`

### 4B) Run on XAMPP (Apache)
- Copy the folder to `htdocs/products-api-yii2`
- Ensure `mod_rewrite` is enabled
- Browse `http://localhost/products-api-yii2/`

## Endpoints

Base path prefix: `/api/products`

- `GET /api/products` — list with filters
  - Query params: `search`, `supplier_id`, `min_price`, `max_price`, `pageSize`
- `GET /api/products/{id}` — get by id
- `POST /api/products` — create one
  - Body JSON: `{ "name":"Pen", "inventory_count":100, "cost":"1.20", "selling_price":"1.99", "supplier_id": 2 }`
- `PUT /api/products/{id}` — full update
- `PATCH /api/products/{id}` — partial update
- `DELETE /api/products/{id}` — delete
- `GET /api/products/search` — same filters as index
- `POST /api/products/bulk` — create many
  - Body JSON: `[ {...}, {...} ]`
- `PATCH /api/products/{id}/adjust-inventory`
  - Body JSON: `{ "delta": 5 }` (negative to decrement; not allowed below zero)

## Postman Examples

- List all:
  - GET `http://localhost:8080/api/products`
- Filter:
  - GET `http://localhost:8080/api/products?search=pen&min_price=1&max_price=10`
- Create:
  - POST `http://localhost:8080/api/products`
  - Body (raw JSON):
    ```json
    { "name":"Marker", "inventory_count":50, "cost":"0.80", "selling_price":"1.50", "supplier_id": 1 }
    ```
- Bulk create:
  - POST `http://localhost:8080/api/products/bulk`
  - Body:
    ```json
    [
      { "name":"Pencil","inventory_count":200,"cost":"0.10","selling_price":"0.25" },
      { "name":"Notebook","inventory_count":80,"cost":"1.00","selling_price":"1.50","supplier_id":3 }
    ]
    ```
- Adjust inventory +7:
  - PATCH `http://localhost:8080/api/products/1/adjust-inventory`
  - Body:
    ```json
    { "delta": 7 }
    ```

## Notes
- CSRF disabled and sessions off for pure API usage.
- Response format is JSON by default.
- If you need auth later, we can add JWT or HTTP Basic easily.

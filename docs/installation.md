# Installation & Deployment Guide
### Production & Development Setup for Tailoring and Boutique Management SaaS

Author: **Imran Ahmed** ([https://imrandev.bd](https://imrandev.bd) | *me@imrandev.bd*)

---

## 1. System Requirements

- **PHP**: 8.3 or higher (8.4/8.5 fully supported)
  - Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `pdo_sqlite`, `tokenizer`, `xml`
- **Database**: MySQL 8.0+ / MariaDB 10.5+ / SQLite 3 (for testing)
- **Node.js**: v18.0 or newer
- **Composer**: 2.6+
- **Cache / Queues**: Redis 6.0+ recommended for high concurrency production installations

---

## 2. Local Development Setup

```bash
# 1. Clone repository
git clone <repository_url> tailoring-and-boutique-management
cd tailoring-and-boutique-management

# 2. Install PHP Composer Dependencies
composer install

# 3. Environment File
cp .env.example .env
php artisan key:generate

# 4. Install Node Dependencies & Compile Frontend Assets
npm install
npm run build

# 5. Run Database Migrations and Seed Demo Personas
php artisan migrate:fresh --seed

# 6. Execute Test Suites
php artisan test

# 7. Start Local Web Server
php artisan serve
```

---

## 3. Production Deployment Notes

### Nginx Configuration Sample
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name atelier.yourdomain.com;
    root /var/www/tailoring-and-boutique-management/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Production Optimizations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

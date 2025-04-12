# College Management System - Deployment Guide

## System Requirements

- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 18.x or higher
- npm 9.x or higher
- Web server (Apache/Nginx)
- SSL certificate for production environment

## Server Setup

### PHP Configuration

Ensure the following PHP extensions are enabled:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD (for image processing)

PHP configuration in `php.ini`:
```ini
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 60
```

### Web Server Configuration

#### Apache

```apache
<VirtualHost *:80>
    ServerName yourcollegedomain.com
    ServerAlias www.yourcollegedomain.com
    DocumentRoot /path/to/college-project/public
    
    <Directory /path/to/college-project/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/college_error.log
    CustomLog ${APACHE_LOG_DIR}/college_access.log combined
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName yourcollegedomain.com
    ServerAlias www.yourcollegedomain.com
    DocumentRoot /path/to/college-project/public
    
    <Directory /path/to/college-project/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/college_error.log
    CustomLog ${APACHE_LOG_DIR}/college_access.log combined
    
    SSLEngine on
    SSLCertificateFile /path/to/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/ssl/private.key
    SSLCertificateChainFile /path/to/ssl/chain.crt
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name yourcollegedomain.com www.yourcollegedomain.com;
    
    # Redirect to HTTPS
    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl;
    server_name yourcollegedomain.com www.yourcollegedomain.com;
    
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    
    root /path/to/college-project/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Optimize static file serving
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|doc)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
    
    client_max_body_size 64M;
}
```

## Deployment Process

### 1. Clone Repository

```bash
cd /path/to/webroot
git clone https://github.com/yourusername/college-project.git
cd college-project
```

### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file to configure:
- Database connection
- Mail server
- Queue connection
- Cache driver
- Session driver
- Storage configuration

### 4. Database Setup

```bash
php artisan migrate --force
php artisan db:seed
```

### 5. File Storage Setup

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### 6. Configure Queue Worker (Optional)

For background processing, set up a queue worker using Supervisor:

```
[program:college-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/college-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/college-project/storage/logs/worker.log
stopwaitsecs=3600
```

### 7. Scheduled Tasks

Add Laravel's scheduler to crontab:

```bash
* * * * * cd /path/to/college-project && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Post-Deployment Checks

- Run `php artisan route:list` to verify routes are working
- Check system logs in `storage/logs/laravel.log`
- Verify database connections and migrations
- Test all core functionality

## Database Migration Plan

### Pre-Migration Steps

1. Back up the current database:
   ```bash
   mysqldump -u username -p database_name > college_backup_$(date +%Y%m%d).sql
   ```

2. Review pending migrations:
   ```bash
   php artisan migrate:status
   ```

### Migration Execution

For production deployment, run migrations with the `--force` flag:
```bash
php artisan migrate --force
```

If issues occur, you can rollback:
```bash
php artisan migrate:rollback
```

### Post-Migration Verification

- Verify the database structure matches expectations
- Run validation tests on critical data
- Check for any orphaned records or inconsistencies

## Backup and Disaster Recovery

### Regular Backups

Set up automated backups:

1. Daily database backups:
   ```bash
   0 2 * * * mysqldump -u username -p database_name | gzip > /path/to/backups/college_db_$(date +\%Y\%m\%d).sql.gz
   ```

2. Weekly file system backups:
   ```bash
   0 3 * * 0 tar -czf /path/to/backups/college_files_$(date +\%Y\%m\%d).tar.gz /path/to/college-project
   ```

3. Retention policy:
   - Keep daily backups for 7 days
   - Keep weekly backups for 4 weeks
   - Keep monthly backups for 1 year

### Recovery Procedures

1. Database recovery:
   ```bash
   mysql -u username -p database_name < backup_file.sql
   ```

2. File system recovery:
   ```bash
   tar -xzf backup_file.tar.gz -C /recovery/path
   ```

3. Application verification after recovery:
   - Run `php artisan route:list` to verify routes
   - Test authentication and critical workflows
   - Verify data integrity

## Security Considerations

- Keep all packages updated regularly
- Set proper file permissions
- Use HTTPS for all connections
- Implement rate limiting for authentication endpoints
- Configure proper CORS policies
- Set secure cookie settings
- Use prepared statements for all database queries
- Apply content security policies
- Schedule regular security audits 
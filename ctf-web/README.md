# 🚩 CTF Web Platform

Platform pembelajaran cybersecurity melalui Capture The Flag challenges.

## 📋 Features

- ✅ User Authentication (Register/Login)
- ✅ Challenge System (SQL Injection, XSS, File Upload, Auth Bypass)
- ✅ Flag Submission & Scoring
- ✅ Leaderboard
- ✅ Vulnerable & Secure Code Comparison

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+ (Native, no framework)
- **Database**: MySQL 5.7+
- **Frontend**: HTML + Tailwind CSS (CDN)
- **Server**: Apache/Nginx

## 📦 Installation

### Local (XAMPP)

1. Clone/download project ke `C:\xampp\htdocs\ctf-web`

2. Import database:
```bash
# Buka phpMyAdmin: http://localhost/phpmyadmin
# Import file: config/init.sql
```

3. Konfigurasi database di `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ctf_platform');
```

4. Start Apache & MySQL dari XAMPP Control Panel

5. Akses: `http://localhost/ctf-web`

### Production (VPS/Shared Hosting)

#### Option 1: Railway (Free Tier)

1. **Persiapan:**
```bash
# Install Git
git init
git add .
git commit -m "Initial commit"
```

2. **Deploy ke Railway:**
- Buka https://railway.app
- Sign up dengan GitHub
- New Project → Deploy from GitHub repo
- Select repo `ctf-web`
- Railway auto-detect PHP project

3. **Setup Database:**
- Add MySQL database dari Railway dashboard
- Copy connection string
- Update `config/db.php` dengan credentials Railway

4. **Environment Variables:**

DB_HOST=mysql.railway.internal
DB_USER=root
DB_PASS=<railway-generated-password>
DB_NAME=railway

5. **Import Database:**
```bash
# Connect via Railway MySQL client
# Import config/init.sql
```

#### Option 2: InfinityFree (Free Hosting)

1. **Sign up:**
- Buka https://infinityfree.net
- Register akun gratis
- Pilih subdomain (e.g., yourname.rf.gd)

2. **Upload Files:**
- Login ke cPanel
- File Manager → htdocs
- Upload semua file KECUALI `config/init.sql`

3. **Setup Database:**
- MySQL Databases → Create database
- Create user & assign ke database
- Note: hostname, username, password, database name

4. **Import Database:**
- phpMyAdmin → Import `config/init.sql`

5. **Update config/db.php:**
```php
define('DB_HOST', 'sql123.byethost.com'); // dari cPanel
define('DB_USER', 'b12_34567890');
define('DB_PASS', 'your_password');
define('DB_NAME', 'b12_34567890_ctf');
```

6. **Set Permissions:**
```bash
chmod 755 challenges/upload/uploads
```

#### Option 3: VPS (DigitalOcean/Vultr)

1. **Create VPS:**
- Ubuntu 22.04 LTS
- Minimal specs: 1GB RAM, 1 vCPU

2. **Install LAMP Stack:**
```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql -y
```

3. **Upload Project:**
```bash
cd /var/www/html
sudo git clone <your-repo-url> ctf-web
sudo chown -R www-data:www-data ctf-web
```

4. **Setup Database:**
```bash
sudo mysql -u root -p
CREATE DATABASE ctf_platform;
USE ctf_platform;
SOURCE /var/www/html/ctf-web/config/init.sql;
exit;
```

5. **Configure Apache:**
```bash
sudo nano /etc/apache2/sites-available/ctf-web.conf
```

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/ctf-web
    
    <Directory /var/www/html/ctf-web>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ctf-error.log
    CustomLog ${APACHE_LOG_DIR}/ctf-access.log combined
</VirtualHost>
```

```bash
sudo a2ensite ctf-web.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

6. **SSL Certificate (Let's Encrypt):**
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d your-domain.com
```

## 🎯 Challenges

### 1. SQL Injection (100 pts)
**Objective:** Bypass login authentication

**Payloads:**
Username: admin' OR '1'='1
Password: anything
Username: admin'--
Password: anything
**Flag:** `FLAG{sql_1nj3ct10n_ez}`

---

### 2. XSS Reflected (150 pts)
**Objective:** Execute JavaScript in victim's browser

**Payloads:**
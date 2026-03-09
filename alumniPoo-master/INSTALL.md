# Guide d'Installation - Alumni CNDA

## 🔧 Installation pas à pas

### Étape 1: Prérequis système

Installez les composants nécessaires:

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install php php-mysql php-mbstring php-xml composer apache2 libapache2-mod-rewrite mysql-server
```

**macOS (avec Homebrew):**
```bash
brew install php mysql composer
```

**Windows:**
- Télécharger PHP 7.4+ depuis php.net
- Installer MySQL Community Server
- Installer Composer depuis getcomposer.org

### Étape 2: Créer la base de données

Connectez-vous à MySQL:
```bash
mysql -u root -p
```

Exécutez:
```sql
CREATE DATABASE cnda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alumni_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
GRANT ALL PRIVILEGES ON cnda.* TO 'alumni_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Étape 3: Cloner et configurer le projet

```bash
# Cloner le repository
git clone <url-du-repo> alumniPoo
cd alumniPoo/alumniPoo-master

# Installer les dépendances Composer
composer install

# Copier et éditer le fichier .env
cp .env.example .env
```

Éditer `.env` avec vos paramètres:
```env
DB_TYPE=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cnda
DB_USER=alumni_user
DB_PASSWORD=votre_mot_de_passe_securise
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Europe/Paris
```

### Étape 4: Initialiser la base de données

```bash
php src/Formulair/DataFixtures/LoadFixtures.php
```

Vous devriez voir:
```
✓ Tous les fixtures ont été chargés avec succès
```

## 🌐 Configuration du serveur web

### Option A: Serveur PHP intégré (développement)

```bash
composer run dev-server
```

Accédez à: `http://localhost:8000/login`

### Option B: Apache (production)

#### 1. Activer mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 2. Créer un VirtualHost:

```apache
# /etc/apache2/sites-available/alumni.conf
<VirtualHost *:80>
    ServerName alumni.example.com
    ServerAdmin admin@example.com

    DocumentRoot /var/www/alumni/web

    <Directory /var/www/alumni/web>
        AllowOverride All
        Require all granted

        # Support du routage
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php?path=$1 [QSA,L]
        </IfModule>
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/alumni_error.log
    CustomLog ${APACHE_LOG_DIR}/alumni_access.log combined
</VirtualHost>
```

#### 3. Activer le site:
```bash
sudo a2ensite alumni.conf
sudo systemctl restart apache2
```

#### 4. Configurer DNS (ou /etc/hosts en local):
```
127.0.0.1    alumni.example.com
```

### Option C: Nginx

```nginx
server {
    listen 80;
    server_name alumni.example.com;
    root /var/www/alumni/web;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔐 Sécurisation en production

### 1. Permissions des fichiers

```bash
# Passer le propriétaire au serveur web
sudo chown -R www-data:www-data /var/www/alumni

# Permissions appropriées
sudo chmod 755 /var/www/alumni
sudo chmod 755 /var/www/alumni/web
sudo chmod 755 /var/www/alumni/src
sudo chmod 755 /var/www/alumni/logs
sudo chmod 644 /var/www/alumni/.env
```

### 2. Variables d'environnement sécurisées

**En production:**
```env
APP_ENV=production
APP_DEBUG=false
```

**Ne jamais committer:**
```bash
# Ajouter à .gitignore
.env
.env.local
logs/
vendor/
```

### 3. HTTPS avec Let's Encrypt

```bash
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d alumni.example.com
```

### 4. Pare-feu

```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable
```

### 5. Configuration PHP (php.ini)

```ini
# Sécurité
display_errors = Off
log_errors = On
error_log = /var/log/php-errors.log

# Sessions
session.save_path = /var/lib/php/sessions
session.cookie_secure = On      # HTTPS seulement
session.cookie_httponly = On    # Pas d'accès JavaScript
session.cookie_samesite = Strict

# Limits
upload_max_filesize = 5M
post_max_size = 5M
max_execution_time = 30

# Extensions
extension_dir = /usr/lib/php/extensions
```

## 🐛 Vérification de l'installation

### Tester la connexion

1. Allez à `http://alumni.example.com/login`
2. Connectez-vous avec:
   - **Login:** admin
   - **Mot de passe:** admin123

### Checker les logs

```bash
# Logs de l'application
tail -f /var/www/alumni/logs/app.log

# Logs Apache
tail -f /var/log/apache2/alumni_access.log
tail -f /var/log/apache2/alumni_error.log

# Logs PHP
tail -f /var/log/php-errors.log
```

### Tester la base de données

```bash
mysql -u alumni_user -p cnda -e "SELECT COUNT(*) FROM users;"
```

## 📊 Sauvegarde et maintenance

### Sauvegarde de la base de données

```bash
# Sauvegarde simple
mysqldump -u alumni_user -p cnda > backup-$(date +%Y%m%d).sql

# Sauvegarde comprimée
mysqldump -u alumni_user -p cnda | gzip > backup-$(date +%Y%m%d).sql.gz
```

### Restauration

```bash
mysql -u alumni_user -p cnda < backup-20250118.sql
```

### Mise à jour des dépendances

```bash
composer update
```

### Réinitialiser les données

```bash
# Supprimer toutes les tables
mysql -u alumni_user -p cnda -e "DROP DATABASE cnda; CREATE DATABASE cnda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Recharger les fixtures
php src/Formulair/DataFixtures/LoadFixtures.php
```

## 🆘 Dépannage

### Erreur: "Cannot connect to database"

**Solution:**
1. Vérifier que MySQL est actif: `sudo systemctl status mysql`
2. Vérifier les credentials dans `.env`
3. Vérifier que la base `cnda` existe: `mysql -u root -p -e "SHOW DATABASES;"`

### Erreur: "Permission denied" sur les logs

**Solution:**
```bash
sudo chmod 755 /var/www/alumni/logs
sudo chown www-data:www-data /var/www/alumni/logs
```

### Erreur 404 sur toutes les routes sauf /login

**Solution:**
1. Vérifier que `mod_rewrite` est activé: `sudo a2enmod rewrite`
2. Vérifier que `.htaccess` est dans `web/`
3. Vérifier que `AllowOverride All` est configuré
4. Redémarrer Apache: `sudo systemctl restart apache2`

### Erreur "Session path not writable"

**Solution:**
```bash
sudo mkdir -p /var/lib/php/sessions
sudo chown www-data:www-data /var/lib/php/sessions
sudo chmod 700 /var/lib/php/sessions
```

### Slow queries

**Solution:**
1. Ajouter des index à la base de données
2. Vérifier les logs d'erreur RedBean: `APP_DEBUG=true` dans `.env`
3. Profiler avec `composer test` et PHPUnit

## 📞 Support

Pour toute question, consultez:
- `README.md` - Documentation générale
- `CLAUDE.md` - Guide technique pour développeurs
- Logs de l'application dans `logs/app.log`

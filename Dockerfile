FROM php:8.4-apache

# 1. Instalar dependencias
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nodejs npm libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Extensiones PHP
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Configuración INFALIBLE de Apache para Render
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Escribimos el VirtualHost directamente, inyectando el puerto y los permisos correctos
RUN echo "<VirtualHost *:\${PORT}>\n\
    DocumentRoot \${APACHE_DOCUMENT_ROOT}\n\
    <Directory \${APACHE_DOCUMENT_ROOT}>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Le decimos a Apache que escuche en el puerto dinámico de Render
RUN echo "Listen \${PORT}" > /etc/apache2/ports.conf

# Habilitamos el módulo de reescritura
RUN a2enmod rewrite

# 4. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Directorio y copia de archivos
WORKDIR /var/www/html
COPY . .

# 6. Instalar dependencias (PHP y Node)
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# 7. Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Inicio
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
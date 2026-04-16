FROM php:8.4-apache

# 1. Instalar dependencias del sistema (Incluye librerías para Postgres y MySQL)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP (Soporte para MySQL y PostgreSQL)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Configurar Apache para Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Ajustar el puerto de Apache para Render (Render asigna un puerto dinámico mediante $PORT)
# Nota: Durante el build usaremos 80 por defecto, pero en ejecución Render inyectará su puerto.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Directorio de trabajo y copiar el proyecto
WORKDIR /var/www/html
COPY . .

# 7. Instalar dependencias de PHP y compilar Assets (Alpine.js / Three.js)
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# 8. Permisos necesarios para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Comando de inicio: Limpia cachés e inicia Apache
CMD php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
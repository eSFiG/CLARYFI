FROM php:8.4-fpm-alpine

RUN apk add --no-cache git unzip nodejs npm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source files
COPY . .

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --no-interaction
RUN composer dump-autoload --optimize

# Install and build Vue frontend
WORKDIR /var/www/html/assets
RUN npm install
RUN npm run build

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]

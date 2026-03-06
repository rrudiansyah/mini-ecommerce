FROM dunglas/frankenphp:latest-php8.3-alpine

# Install PHP extensions yang dibutuhkan
RUN install-php-extensions \
    pdo \
    pdo_mysql \
    opcache \
    intl \
    zip \
    gd \
    exif \
    fileinfo

# Install bash & tools tambahan
RUN apk add --no-cache bash tzdata

# Set timezone ke WIB
ENV TZ=Asia/Jakarta

# Set working directory
WORKDIR /app

# Copy semua file project
COPY . .

# Buat folder uploads dan set permission
RUN mkdir -p public/uploads \
    && chmod -R 775 public/uploads \
    && chown -R www-data:www-data public/uploads

# Copy konfigurasi FrankenPHP (Caddyfile)
COPY docker/Caddyfile /etc/caddy/Caddyfile

# Expose port
EXPOSE 80 443

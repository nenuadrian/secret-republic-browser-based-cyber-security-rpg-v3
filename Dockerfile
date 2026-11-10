# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Secret Republic V3 - PHP 8.5 FPM + Nginx (MySQL or SQLite)
# ---------------------------------------------------------------------------
FROM php:8.5-fpm-trixie

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx supervisor unzip curl \
        libpng-dev libjpeg-dev libfreetype6-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mysqli pdo_mysql pdo_sqlite \
    && rm -rf /var/lib/apt/lists/* /etc/nginx/sites-enabled/default

# PHP: production defaults, app overrides, and let php-fpm see the container's
# environment (DB_*, SMTP_*, CRON_KEY, ...)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf '[www]\nclear_env = no\n' > /usr/local/etc/php-fpm.d/zz-env.conf
COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-secretrepublic.ini"
COPY docker/nginx.conf /etc/nginx/sites-enabled/secretrepublic.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app

# Dependencies first so this layer is cached between code changes
COPY composer.json ./
RUN composer install --prefer-dist --no-dev --no-interaction --no-progress --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p includes/templates_c includes/cache includes/data \
    && chown -R www-data:www-data includes \
    && rm /usr/bin/composer

COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/secretrepublic-entrypoint

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --start-interval=2s --retries=3 \
    CMD curl -fsS -o /dev/null http://127.0.0.1/ || exit 1

ENTRYPOINT ["secretrepublic-entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

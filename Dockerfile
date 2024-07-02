FROM dunglas/frankenphp:1-php8.3-bookworm

RUN apt-get update -y && apt-get install -y supervisor

RUN install-php-extensions \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    hash \
    mbstring \
    openssl \
    pcre \
    pdo \
    pdo_mysql \
    session \
    tokenizer \
    xml \
    redis
    # Add other PHP extensions here...
RUN install-php-extensions pcntl 

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app

COPY .env.production /app/.env

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN install-php-extensions zip

RUN composer install --optimize-autoloader --no-dev

RUN php artisan optimize

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
# ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
# CMD ["php", "artisan", "queue:work"]
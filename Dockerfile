FROM dunglas/frankenphp:1-php8.3-bookworm

RUN apt-get update -y && apt-get install -y supervisor

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN install-php-extensions \
    pcntl \
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
    redis \
    zip

COPY . /app

COPY .env.production /app/.env

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --optimize-autoloader --no-dev

RUN php artisan optimize

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
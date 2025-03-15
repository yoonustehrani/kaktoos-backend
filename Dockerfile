FROM node:18-alpine AS base

# npm install
FROM base AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app
# Install dependencies based on the preferred package manager
# yarn.lock* pnpm-lock.yaml*
COPY package.json package-lock.json* ./
RUN \
  if [ -f package-lock.json ]; then npm ci; \
  else echo "Lockfile not found." && exit 1; \
  fi

# npm run build
FROM base AS static-builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
# Could be improved using only the needed files for build
COPY . .
RUN \
  if [ -f package-lock.json ]; then npm run build; \
  else echo "Lockfile not found." && exit 1; \
  fi

# Serving the app
FROM dunglas/frankenphp:1-php8.3-bookworm

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
RUN php artisan storage:link

RUN apt-get update -y && apt-get install -y supervisor

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY --from=static-builder /app/public/build ./public/build

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

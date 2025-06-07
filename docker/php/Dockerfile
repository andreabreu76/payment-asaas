FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

ARG NEW_RELIC_AGENT_VERSION=10.10.0.1
ARG NEW_RELIC_LICENSE_KEY
ARG NEW_RELIC_APP_NAME
ARG NEW_RELIC_DAEMON_ADDRESS=newrelic:31339

RUN curl -L https://download.newrelic.com/php_agent/archive/${NEW_RELIC_AGENT_VERSION}/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-linux.tar.gz | tar -C /tmp -zx \
    && export NR_INSTALL_USE_CP_NOT_LN=1 \
    && export NR_INSTALL_SILENT=1 \
    && /tmp/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-linux/newrelic-install install \
    && rm -rf /tmp/newrelic-php5-* /tmp/nrinstall*

RUN sed -i \
  -e "s/newrelic.license[[:space:]]*=[[:space:]]*.*/newrelic.license = ${NEW_RELIC_LICENSE_KEY}/" \
  -e "s/newrelic.appname[[:space:]]*=[[:space:]]*.*/newrelic.appname = ${NEW_RELIC_APP_NAME}/" \
  -e "s/;newrelic.daemon.address[[:space:]]*=[[:space:]]*.*/newrelic.daemon.address = ${NEW_RELIC_DAEMON_ADDRESS}/" \
  /usr/local/etc/php/conf.d/newrelic.ini


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy custom entrypoint script
COPY docker-php-entrypoint-custom /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-php-entrypoint-custom

# Set working directory
WORKDIR /var/www

# Create the working directory with proper permissions
RUN mkdir -p /var/www && chown -R www-data:www-data /var/www

# Set permissions for New Relic configuration files
RUN chmod -R 777 /usr/local/etc/php/conf.d/

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

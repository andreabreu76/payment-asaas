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

# Install NewRelic
RUN curl -L https://download.newrelic.com/php_agent/release/newrelic-php5-10.x.x.x-linux.tar.gz | tar -C /tmp -zx \
    && export NR_INSTALL_USE_CP_NOT_LN=1 \
    && export NR_INSTALL_SILENT=1 \
    && /tmp/newrelic-php5-*/newrelic-install install \
    && rm -rf /tmp/newrelic-php5-* \
    && sed -i \
        -e 's/"REPLACE_WITH_REAL_KEY"/"${NEW_RELIC_LICENSE_KEY}"/' \
        -e 's/newrelic.appname = "PHP Application"/"${NEW_RELIC_APP_NAME}"/' \
        -e 's/;newrelic.daemon.address = ""/"newrelic.daemon.address = "${NEW_RELIC_DAEMON_ADDRESS}"/' \
        /usr/local/etc/php/conf.d/newrelic.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

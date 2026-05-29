FROM php:5.6-apache

# Install mysql extensions
RUN docker-php-ext-install mysql mysqli

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy apache config
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

# Fix permissions
RUN chown -R www-data:www-data /var/www/html
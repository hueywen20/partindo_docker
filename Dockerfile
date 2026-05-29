FROM php:5.6-apache

# Install mysql extension (the old mysql_* functions)
RUN docker-php-ext-install mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Stage 1: Composer build stage
FROM composer:2.5 as build
COPY . /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Stage 2: PHP with Apache for production
FROM php:8.4-apache as production

# Set environment variables for production
#ENV APP_ENV=production
#ENV APP_DEBUG=false
# Install required PHP extensions
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql

# Copy custom opcache configuration
COPY docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy app files from the build stage
COPY --from=build /app /var/www/html

# Copy Apache site configuration
COPY docker/apache2/ufood.conf /etc/apache2/sites-available/000-default.conf

# Copy production environment .env file
COPY .env.prod /var/www/html/.env

# Prepare the Laravel application for production
RUN \
    chmod -R 775 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/storage
# Command to start Supervisor, which will manage the processes
#COPY docker/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh
#RUN chmod +x /usr/local/bin/entrypoint.sh
#RUN php artisan app:prepare-evaluation-db
#RUN php artisan optimize:clear && \
#    php artisan optimize

# Install Supervisor
RUN apt-get update && apt-get install -y \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Copy your supervisor config file into the container
COPY docker/supervisor.conf /etc/supervisor/supervisord.conf
RUN chmod 644 /etc/supervisor/supervisord.conf

# Run supervisord as the container's entrypoint
#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
#CMD ["sh", "-c", "rm -f /var/run/supervisor.sock && /usr/bin/supervisord -c /etc/supervisor/supervisord.conf"]
# Copy the entrypoint script into the container
COPY docker/scripts/entrypoint.sh /usr/local/bin/entrypoint.sh

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 80
EXPOSE 80

# Enable Apache mod_rewrite
RUN a2enmod rewrite
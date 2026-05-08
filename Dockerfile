FROM richarvey/php-apache-heroku:latest

# Salin kode project ke dalam container
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Jalankan composer install (Tanpa NPM)
RUN composer install --no-dev --optimize-autoloader

# Set environment variabel untuk port
ENV PORT 80
EXPOSE 80

# Pastikan Laravel bisa menulis ke folder storage
RUN chmod -R 777 storage bootstrap/cache